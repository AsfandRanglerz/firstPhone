<?php

namespace App\Repositories\Api;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\CheckOut;
use App\Models\OrderItem;
use App\Models\MobileModel;
use Illuminate\Support\Str;
use App\Models\VendorMobile;
use Illuminate\Http\Request;
use App\Models\DeviceReceipt;
use App\Models\MobileListing;
use App\Models\ShippingAddress;
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
        ->get()
        ->map(function ($order) {

            $order->items = $order->items->map(function ($item) {

                // Decode images
                $images = json_decode($item->product->image ?? '[]', true);
                $videos = json_decode($item->product->video ?? '[]', true);

                $item->product->image = collect($images)
                    ->map(fn ($path) => asset($path))
                    ->values();

                $item->product->video = collect($videos)
                    ->map(fn ($path) => asset($path))
                    ->values();

                return $item;
            });

            return $order;
        });
}


    public function getOrdersByVendorAndStatus(int $vendorId, string $status): Collection
    {
        return Order::with(['items.product', 'items.vendor'])
            ->whereHas('items', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
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

    public function getSalesReport(int $vendorId): array
    {
        // Helper function to calculate totals
        $calculateTotals = function ($type = 'overall') use ($vendorId) {
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
        };

        return [
            'today'   => $calculateTotals('today'),
            'overall' => $calculateTotals('overall'),
        ];
    }


    public function getOrderStatistics(int $vendorId): array
    {
        $todayDate = now()->format('Y-m-d');

        // Aaj ke orders (vendor ke products)
        $todayOrders = Order::whereHas('items', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->whereDate('created_at', $todayDate)
            ->select(
                'id',
                'order_number',
                'payment_status',
                'order_status',
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as formatted_date")
            )
            ->get();

        // Overall orders (vendor ke sab)
        $overallOrders = Order::whereHas('items', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->select(
                'id',
                'order_number',
                'payment_status',
                'order_status',
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as formatted_date")
            )
            ->get();

        return [
            'today_orders'   => $todayOrders,
            'overall_orders' => $overallOrders,
        ];
    }




    // Repository
    // public function getorderlist($orderId)
    // {
    //     $order = Order::with(['items', 'customer', 'shippingAddress'])
    //         ->findOrFail($orderId);

    //     $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
    //     $shippingCharges = $order->shipping_charges ?? 0;
    //     $total = $subtotal + $shippingCharges;

    //     return [
    //         'order_id'       => $order->id,
    //         'customer'       => [
    //             'name'          => $order->shippingAddress->name ?? $order->customer->name,
    //             'email'         => $order->shippingAddress->email ?? $order->customer->email,
    //             'phone_number'  => $order->shippingAddress->phone_number ?? $order->customer->phone,
    //             'city'          => $order->shippingAddress->city ?? null,
    //             'postal_code'   => $order->shippingAddress->postal_code ?? null,
    //             'street_address' => $order->shippingAddress->street_address ?? null,
    //         ],
    //         'products'       => $order->items->map(fn($item) => [
    //             'product_name' => ($item->product->brand->name ?? '') . ' ' . ($item->product->model->name ?? $item->product_name),
    //             'price'        => $item->price,
    //             'quantity'     => $item->quantity,
    //             'image'        => $item->product->image
    //                 ? asset(
    //                     is_array(json_decode($item->product->image, true))
    //                         ? ltrim(json_decode($item->product->image, true)[0], '/')   // ✅ First from JSON array
    //                         : ltrim(explode(',', $item->product->image)[0], '/')       // ✅ First from comma string
    //                 )
    //                 : null,
    //         ]),
    //         'order_status'   => $order->order_status,
    //         'payment_status' => $order->payment_status,
    //         'delivery_method' => $order->delivery_method,
    //         'subtotal'       => $subtotal,
    //         'shipping'       => $shippingCharges,
    //         'total'          => $total,
    //     ];
    // }

    public function getorderlist()
{
    $userId = Auth::id();
    

   if (!$userId) {
        throw new \Exception('Unauthorized', 401);
    }


    // 👉 Get all checkout items of this user
    $checkoutItems = CheckOut::where('user_id', $userId)->get();

    
    if ($checkoutItems->isEmpty()) {
        throw new \Exception('No checkout items found', 404);
    }

    $uniqueItems = $checkoutItems->unique(function ($item) {
    return $item->brand_name
        . '|' . $item->model_name
        . '|' . $item->price
        . '|' . $item->quantity;
    })->values();


    // 👉 Get shipping address of this user
    $shipping = ShippingAddress::where('customer_id', $userId)->first();

    // 💰 Subtotal calculation
    $subtotal = $uniqueItems->sum(function ($item) {
        return ($item->price ?? 0) * ($item->quantity ?? 0);
    });

    $shippingCharges = 0; // or fetch dynamically if needed
    $total = $subtotal + $shippingCharges;

    return [

        'user_id' => $userId,

        // ⭐ CUSTOMER DATA
        'customer' => [
            'name'           => $shipping->name ?? null,
            'email'          => $shipping->email ?? null,
            'phone_number'   => $shipping->phone ?? null,
            'city'           => $shipping->city ?? null,
            'postal_code'    => $shipping->postal_code ?? null,
            'street_address' => $shipping->street_address ?? null,
        ],

        // ⭐ PRODUCTS FROM CHECKOUT TABLE
        'products' => $uniqueItems->map(function ($item) {

            $vendor = Vendor::where('name', $item->vendor_name)->first();
            $brand = Brand::where('name', $item->brand_name)->first();
            $model = MobileModel::where('name', $item->model_name)->first();

            $productId = null;

            if ($vendor && $brand && $model) {
                $vendorMobile = VendorMobile::where('vendor_id', $vendor->id)
                    ->where('brand_id', $brand->id)
                    ->where('model_id', $model->id)
                    ->first();

                if ($vendorMobile) {
                    $productId = $vendorMobile->id;
                    $vendorId  = $vendorMobile->vendor_id;
                }
            }


            return [
                'product_id' => $productId,
                'vendor_id'  => $vendorId,
                'shop_name'  => $item->vendor_name,
                'brand_name' => $item->brand_name,
                'model_name' => $item->model_name,
                'price'      => $item->price,
                'quantity'   => $item->quantity,
                'image'        => $item->image
                    ? asset(
                        is_array(json_decode($item->image, true))
                            ? ltrim(json_decode($item->image, true)[0], '/')   // ✅ First from JSON array
                            : ltrim(explode(',', $item->image)[0], '/')       // ✅ First from comma string
                    )
                    : null,
            ];
        }),

        'subtotal' => $subtotal,
        'shipping' => $shippingCharges,
        'total'    => $total,
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
