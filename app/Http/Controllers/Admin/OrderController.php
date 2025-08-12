<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->latest()->get();

        // Define possible statuses
        $statuses = ['pending', 'confirmed', 'in_progress', 'shipped', 'delivered', 'cancelled'];
        return view('admin.order.index', compact('orders', 'statuses'));
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
}
