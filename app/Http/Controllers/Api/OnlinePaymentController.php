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
            $productCount = 0;
            $products = $request->products;

            if (isset($products['product_id'])) {
                // Single product case
                $totalAmount = $products['price'];
                $productCount = 1;
                $firstProductId = $products['product_id'];
                $firstVendorId = $products['vendor_id'];
            } else {
                // Multiple products case
                $firstProductId = $products[0]['product_id'];
                $firstVendorId = $products[0]['vendor_id'];

                foreach ($products as $product) {
                    if (
                        !isset($product['product_id']) || !isset($product['vendor_id']) ||
                        !isset($product['price'])
                    ) {
                        throw new \Exception('Invalid product data structure');
                    }
                    $totalAmount += $product['price'];
                    $productCount++;
                }
            }

            // Create order
            $order = Order::create([
                'customer_id'      => auth()->id(),
                'order_number'     => rand(100000, 999999),
                'shipping_address' => $request->shipping_address,
                'payment_status'   => 'pending',
                'order_status'     => 'pending',
                'delivery_method'  => $request->delivery_method,
            ]);

            // Create order item
            $orderItem = OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $firstProductId,
                'vendor_id'  => $firstVendorId,
                'quantity'   => $productCount,
                'price'      => $totalAmount,
            ]);

            // ✅ Notification to Vendor
            $vendor = \App\Models\Vendor::find($firstVendorId);
            if ($vendor && !empty($vendor->fcm_token)) {
                \App\Helpers\NotificationHelper::sendFcmNotification(
                    $vendor->fcm_token,
                    "New Order Received",
                    "You have received a new order #{$order->order_number} with {$productCount} product(s), Total: Rs {$totalAmount}",
                    [
                        'order_id'     => (string) $order->id,
                        'order_number' => (string) $order->order_number,
                        'total_amount' => (string) $totalAmount,
                        'products'     => (string) $productCount,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order placed successfully & vendor notified',
                'data'    => [
                    'order' => $order,
                    'order_item' => [
                        'product_id'     => $firstProductId,
                        'vendor_id'      => $firstVendorId,
                        'total_products' => $productCount,
                        'total_amount'   => $totalAmount
                    ]
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
