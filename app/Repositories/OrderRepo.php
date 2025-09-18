<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepoInterface;
use Illuminate\Http\Request;

class OrderRepo implements OrderRepoInterface
{
    public function getAllOrders()
    {
        return Order::with(['customer', 'items.product.brand', 'items.product.model', 'items.vendor'])
            ->latest()
            ->get();
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return true;
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|in:pending,confirmed,in_progress,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();

        return $order;
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,pending,failed,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return $order;
    }

    public function pendingOrdersCount()
    {
        return Order::where('order_status', 'inprogress')->count();
    }

    public function getTotals()
    {
        $orders = Order::with('items')
            ->where('payment_status', 'paid')
            ->get();

        $codTotal = $orders->where('delivery_method', 'cod')
            ->flatMap->items
            ->sum(fn($item) => $item->price * $item->quantity);

        $onlineTotal = $orders->where('delivery_method', 'online')
            ->flatMap->items
            ->sum(fn($item) => $item->price * $item->quantity);

        $pickupTotal = $orders->where('delivery_method', 'go_shop')
            ->flatMap->items
            ->sum(fn($item) => $item->price * $item->quantity);

        $total = $orders
            ->flatMap->items
            ->sum(fn($item) => $item->price * $item->quantity);

        return [
            'total' => $total,
            'codTotal' => $codTotal,
            'onlineTotal' => $onlineTotal,
            'pickupTotal' => $pickupTotal,
        ];
    }
}
