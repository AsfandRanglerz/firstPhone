<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Helpers\ResponseHelper;
use App\Repositories\Api\Interfaces\OrderRepositoryInterface;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

       public function index(Request $request)
    {
        try {
            $status = $request->get('status');
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 401);
            }
            if (!$status) {
                return ResponseHelper::error(null, 'Status parameter is required', 400);
            }
            $orders = $this->orderRepository->getOrdersByCustomerAndStatus($customerId, $status);
            return ResponseHelper::success($orders, 'Orders fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }



     public function show($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 401);
            }
            $order = $this->orderRepository->getOrderWithRelations($id, $customerId);
            if (!$order) {
                return ResponseHelper::error(null, 'Order not found', 404);
            }
            $data = [
                'order' => $order,
                'total_amount' => $order->items->sum(fn($item) => $item->price * $item->quantity),
            ];
            return ResponseHelper::success($data, 'Order details fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }


    public function track($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 401);
            }
            $order = $this->orderRepository->getOrderByIdAndCustomer($id, $customerId);
            if (!$order) {
                return ResponseHelper::error(null, 'Order not found', 404);
            }
            $tracking = [
                ['stage' => 'Order Placed', 'date' => $order->created_at->format('d M Y')],
                ['stage' => 'In Progress', 'date' => now()->subDays(5)->format('d M Y')],
                ['stage' => 'Shipped', 'date' => now()->subDays(2)->format('d M Y')],
                ['stage' => 'Delivered', 'date' => now()->format('d M Y')],
            ];
            return ResponseHelper::success([
                'order_id' => $order->id,
                'status' => $order->order_status,
                'tracking' => $tracking
            ], 'Order tracking fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }


}
