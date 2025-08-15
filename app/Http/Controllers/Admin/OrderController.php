<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'items.product.brand', 'items.product.model', 'items.vendor'])
            ->latest()
            ->get();

        $statuses = ['pending', 'confirmed', 'in_progress', 'shipped', 'delivered', 'cancelled'];

        // Calculate totals based on order items instead of total_amount field
        $codTotal = $orders->where('delivery_method', 'cod')->flatMap->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $onlineTotal = $orders->where('delivery_method', 'online')->flatMap->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $pickupTotal = $orders->where('delivery_method', 'pickup')->flatMap->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('admin.order.index', compact('orders', 'statuses', 'codTotal', 'onlineTotal', 'pickupTotal'));
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('order.index')->with('success', 'Order Deleted Successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|in:pending,confirmed,in_progress,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json([
            'success' => true,
            'new_status' => $order->order_status,
            'message' => 'Order status updated successfully'
        ]);
    }

    // OrderController.php
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,pending,failed,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
        ]);
    }

    public function pendingCounter()
    {
        $count = Order::where('order_status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
}
