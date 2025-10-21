<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlinePaymentController extends Controller
{
    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            // $products = $request->products;
            $products = $request->products ?? $request->all();

            // Sequential Order Number
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $newOrderNumber = $lastOrder ? $lastOrder->order_number + 1 : 10000000;

            // Create Order
            $order = Order::create([
                'customer_id'      => auth()->id(),
                'order_number'     => $newOrderNumber,
                'shipping_address' => $request->shipping_address,
                'payment_status'   => 'pending',
                'order_status'     => 'pending',
                'delivery_method'  => $request->delivery_method,
            ]);

            $vendorIds = [];

            // Handle Single Product
            if (isset($products['product_id'])) {
                $totalAmount = $products['price'];
                $vendorIds[] = $products['vendor_id'];

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $products['product_id'],
                    'vendor_id'  => $products['vendor_id'],
                    'quantity'   => $products['quantity'] ?? 1,
                    'price'      => $products['price'],
                ]);
            } else {
                // Handle Multiple Products
                foreach ($products as $product) {
                    $vendorIds[] = $product['vendor_id'];

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product['product_id'],
                        'vendor_id'  => $product['vendor_id'],
                        'quantity'   => $product['quantity'] ?? 1,
                        'price'      => $product['price'],
                    ]);

                    $totalAmount += $product['price'];
                }
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            // Notify all unique vendors
            foreach (array_unique($vendorIds) as $vendorId) {
                $vendor = \App\Models\Vendor::find($vendorId);
                if ($vendor && !empty($vendor->fcm_token)) {
                    \App\Helpers\NotificationHelper::sendFcmNotification(
                        $vendor->fcm_token,
                        "New Order Received",
                        "You have received a new order #{$order->order_number}, Total: Rs {$totalAmount}",
                        [
                            'order_id'     => (string) $order->id,
                            'order_number' => (string) $order->order_number,
                            'total_amount' => (string) $totalAmount,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order placed successfully & vendors notified',
                'data'    => [
                    'order' => $order,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
