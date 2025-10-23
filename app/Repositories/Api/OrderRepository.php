<?php

namespace App\Repositories\Api;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\VendorMobile;
use Illuminate\Http\Request;
use App\Models\DeviceReceipt;
use App\Models\MobileListing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getOrdersByCustomerAndStatus(int $customerId, string $status): Collection
    {
        return Order::with(['items.product', 'items.vendor'])
            ->where('customer_id', $customerId)
            ->where('order_status', $status)
            ->latest()
            ->get();
    }

    public function getOrderWithRelations(int $orderId, int $customerId): Order
    {
        return Order::with(['items.product', 'items.vendor'])
            ->where('customer_id', $customerId)
            ->findOrFail($orderId);
    }

    public function getOrderByIdAndCustomer(int $orderId, int $customerId): Order
    {
        return Order::where('customer_id', $customerId)
            ->findOrFail($orderId);
    }

    public function getOrderByIdAndVendor(int $orderId, int $vendorId): Order
    {
        return Order::where('id', $orderId)
            ->whereHas('items', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->firstOrFail();
    }

    public function getSalesReport(int $vendorId, string $type = 'overall'): array
    {
        $query = OrderItem::where('vendor_id', $vendorId)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->join('orders', 'orders.id', '=', 'order_items.order_id');


        if ($type === 'today') {
            $query->whereDate('orders.created_at', now()->toDateString());
        }

        $totals = $query->selectRaw('orders.delivery_method, SUM(order_items.price * order_items.quantity) as total')
            ->groupBy('orders.delivery_method')
            ->pluck('total', 'orders.delivery_method')
            ->toArray();

        return [
            'cod_orders_total'    => $totals['cod']    ?? 0,
            'online_orders_total' => $totals['online'] ?? 0,
            'pickup_orders_total' => $totals['pickup'] ?? 0,
            'grand_total'         => array_sum($totals),
        ];
    }


    public function getOrderStatistics(?string $date = null): Collection
    {
        if ($date) {
            try {
                $formattedDate = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid date format, expected DD-MM-YYYY');
            }
        }

        $orders = Order::select(
                'id',
                'order_status',
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as formatted_date")
            )
            ->when($date, function ($query) use ($formattedDate) {
                $query->whereDate('created_at', '=', $formattedDate);
            })
            ->get();

        return $orders;
    }



    // Repository
    public function getorderlist($orderId)
    {
        $order = Order::with(['items', 'customer', 'shippingAddress'])
            ->findOrFail($orderId);

        $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
        $shippingCharges = $order->shipping_charges ?? 0;
        $total = $subtotal + $shippingCharges;

        return [
            'order_id'       => $order->id,
            'customer'       => [
                'name'          => $order->shippingAddress->name ?? $order->customer->name,
                'email'         => $order->shippingAddress->email ?? $order->customer->email,
                'phone_number'  => $order->shippingAddress->phone_number ?? $order->customer->phone,
                'city'          => $order->shippingAddress->city ?? null,
                'postal_code'   => $order->shippingAddress->postal_code ?? null,
                'street_address' => $order->shippingAddress->street_address ?? null,
            ],
            'products'       => $order->items->map(fn($item) => [
                'product_name' => ($item->product->brand->name ?? '') . ' ' . ($item->product->model->name ?? $item->product_name),
                'price'        => $item->price,
                'quantity'     => $item->quantity,
                'image'        => $item->product->image
                    ? asset(
                        is_array(json_decode($item->product->image, true))
                            ? ltrim(json_decode($item->product->image, true)[0], '/')   // ✅ First from JSON array
                            : ltrim(explode(',', $item->product->image)[0], '/')       // ✅ First from comma string
                    )
                    : null,
            ]),
            'order_status'   => $order->order_status,
            'payment_status' => $order->payment_status,
            'delivery_method' => $order->delivery_method,
            'subtotal'       => $subtotal,
            'shipping'       => $shippingCharges,
            'total'          => $total,
        ];
    }

    public function createDeviceReceipts(int $orderId, array $devices): array
    {
        $vendor = Auth::id();

        // ✅ Load the order with items
        $order = Order::with(['items'])->findOrFail($orderId);

        $createdReceipts = [];

        foreach ($devices as $device) {
            // ✅ Make sure the product_id exists in this order
            $item = $order->items->where('product_id', $device['product_id'])->first();

            if (!$item) {
                throw new \Exception("Product not found in this order");
            }

            // ✅ Fetch product details from mobile listing
            $mobile = VendorMobile::with(['brand', 'model'])->find($item->product_id);

            if (!$mobile) {
                throw new \Exception("Mobile listing not found for product_id: " . $item->product_id);
            }

            $paymentId = strtoupper(Str::random(12));

            // Ensure uniqueness in DB
            while (DeviceReceipt::where('payment_id', $paymentId)->exists()) {
                $paymentId = strtoupper(Str::random(12));
            }

            // ✅ Create receipt
            $receipt = DeviceReceipt::create([
                'order_id'   => $orderId,
                'order_item_id' => $item->id,
                'product_id' => $mobile->id,
                'brand_id'      => $mobile->brand_id ?? 'Unknown',
                'model_id'      => $mobile->model_id ?? 'Unknown',
                'imei_one'      => $device['imei_one'] ?? null,
                'imei_two'      => $device['imei_two'] ?? null,
                'payment_id'   => $paymentId,
            ]);

            $createdReceipts[] = $receipt;
        }

        return $createdReceipts;
    }

    public function getReceiptById(int $deviceReceiptId): array
    {
        $deviceReceipt = DeviceReceipt::with([
            'order.customer',
            'order.vendor',
            'order.items.deviceReceipts',
            'order.items.vendor',
        ])->findOrFail($deviceReceiptId);

        $order = $deviceReceipt->order;

        $vendorName = optional($order->items->first()->vendor)->name;
        $deviceReceipt->created_at_formatted = $deviceReceipt->created_at->format('d-m-Y H:i:s');
        $response = [
            'order_number'    => $order->order_number,
            'delivery_method' => $order->delivery_method,
            'from_customer'   => $order->customer?->name,
            'to_vendor'       => $vendorName,
            'payment_id'     => $deviceReceipt->payment_id,
            'total_products'  => $order->items->count(),
            'products'        => [],
            'created_at'      => $deviceReceipt->created_at_formatted,
        ];

        foreach ($order->items as $item) {
            foreach ($item->deviceReceipts as $receipt) {
                $response['products'][] = [
                    'brand'    => $receipt->brand,
                    'model'    => $receipt->model,
                    'imei_one' => $receipt->imei_one,
                    'imei_two' => $receipt->imei_two,
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'total'    => $item->quantity * $item->price,
                ];
            }
        }

        $response['total_amount'] = collect($response['products'])->sum('total');

        return $response;
    }
}
