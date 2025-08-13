<?php

namespace App\Repositories\Api;

use App\Repositories\Api\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
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
}
