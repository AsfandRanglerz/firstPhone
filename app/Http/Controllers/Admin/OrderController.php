<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Middleware\admin;
use App\Models\CancelOrder;
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
        $statuses = ['inprogress', 'shipped', 'delivered', 'cancelled'];

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

    public function cancel_order()
    {
        $cancelOrders = CancelOrder::with([
            'order.customer',
            'orderItem.vendor'
        ])->latest()->get();

        return view('admin.order.cancel', compact('cancelOrders'));
    }

    public function updateCancelStatus(Request $request, $id)
    {
        $cancelOrder = CancelOrder::findOrFail($id);

        if ($request->hasFile('proof_file_image')) {
            $file = $request->file('proof_file_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cancel_proofs'), $filename);
            $cancelOrder->proof_file_image = 'uploads/cancel_proofs/' . $filename;
        }

        $cancelOrder->status = $request->status ?? 'approved';
        $cancelOrder->save();

        return response()->json([
            'success' => true,
            'message' => 'Cancel order status updated successfully.'
        ]);
    }

    public function checkDeliveryStatus($id)
    {
        $cancelOrder = CancelOrder::with('order')->findOrFail($id);

        if ($cancelOrder->order->delivery_method === 'online') {
            return response()->json([
                'delivery_method' => 'online'
            ]);
        }

        $cancelOrder->status = 'approved';
        $cancelOrder->save();

        return response()->json([
            'delivery_method' => 'approved_direct'
        ]);
    }


    public function pendingCancelOrderCounter()
    {
        $count = CancelOrder::where('status', 'requested')->count();

        return response()->json(['count' => $count]);
    }


    public function deleteCancelOrder($id)
    {
        $cancelOrder = CancelOrder::findOrFail($id);
        $cancelOrder->delete();

        return redirect()->route('cancel-orders.index')
            ->with('success', 'Cancel order deleted successfully.');
    }
}
