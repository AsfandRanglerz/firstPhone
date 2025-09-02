<?php

namespace App\Repositories\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection; // <-- correct import
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
        return Order::select('id', 'order_status', 'created_at')
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->get();
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
            'street_address'=> $order->shippingAddress->street_address ?? null,
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
        'delivery_method'=> $order->delivery_method,
        'subtotal'       => $subtotal,
        'shipping'       => $shippingCharges,
        'total'          => $total,
    ];
}



}
