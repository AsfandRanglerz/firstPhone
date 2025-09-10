<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderRepoInterface;

class OrderController extends Controller
{
    protected $orderRepo;

    public function __construct(OrderRepoInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        $orders = $this->orderRepo->getAllOrders();
        $statuses = ['pending', 'confirmed', 'in_progress', 'shipped', 'delivered', 'cancelled'];

        $totals = $this->orderRepo->getTotals();

        return view('admin.order.index', [
            'orders'      => $orders,
            'statuses'    => $statuses,
            'total'       => $totals['total'],
            'codTotal'    => $totals['codTotal'],
            'onlineTotal' => $totals['onlineTotal'],
            'pickupTotal' => $totals['pickupTotal'],
        ]);
    }


    public function destroy($id)
    {
        $this->orderRepo->deleteOrder($id);
        return redirect()->route('order.index')->with('success', 'Order Deleted Successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = $this->orderRepo->updateOrderStatus($request, $id);

        $vendor = $order->vendor;
        if ($vendor && $vendor->fcm_token) {
            NotificationHelper::sendFcmNotification(
                $vendor->fcm_token,
                "Order Status Updated",
                "Order #{$order->id} status changed to {$order->order_status}.",
                [
                    'order_id' => $order->id,
                    'new_status' => $order->order_status
                ]
            );
        }

        return response()->json([
            'success' => true,
            'new_status' => $order->order_status,
            'message' => 'Order Status Updated Successfully'
        ]);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $this->orderRepo->updatePaymentStatus($request, $id);

        return response()->json([
            'success' => true,
            'message' => 'Payment Status Updated Successfully',
        ]);
    }

    public function pendingCounter()
    {
        $count = $this->orderRepo->pendingOrdersCount();
        return response()->json(['count' => $count]);
    }

    public function getTotals()
    {
        $total = $this->orderRepo->getTotals();
        return response()->json($total);
    }
}
