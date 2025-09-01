<?php

namespace App\Repositories\Api;

use App\Repositories\Api\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection; // <-- correct import

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

    public function getSalesReport(int $vendorId, ?string $deliveryMethod = null): Collection
    {
        $query = Order::where('payment_status', '=', 'paid')
            ->whereHas('items', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });

        if (!empty($deliveryMethod)) {
            $query->where('delivery_method', $deliveryMethod);
        }

        return $query->with([
            'customer',
            'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)->with('product', 'vendor');
            }
        ])
            ->latest()
            ->get();
    }

    public function getOrderStatistics(?string $date = null): Collection
    {
        return Order::select('id', 'order_status', 'created_at')
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->get();
    }
}
