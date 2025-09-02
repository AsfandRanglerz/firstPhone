<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ShippingAddressRequest;
use App\Models\ShippingAddress;
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




    public function getOrderStatistics(Request $request)
    {
        try {
            $date = $request->query('date');

            $orders = $this->orderRepository->getOrderStatistics($date);

            return ResponseHelper::success([
                'date'   => $date,
                'orders' => $orders
            ], 'Order statistics fetched successfully', 200);
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

    public function shippingAddress(ShippingAddressRequest $request)
    {
        try {
            $shippingAddress = ShippingAddress::create($request->validated());
            return ResponseHelper::success($shippingAddress, 'Shipping address saved successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }

    public function getShippingAddress()
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 401);
            }
            $address = ShippingAddress::where('customer_id', $customerId)->latest()->first();
            if (!$address) {
                return ResponseHelper::error(null, 'No shipping address found', 404);
            }
            return ResponseHelper::success($address, 'Shipping address fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }

    public function deleteShippingAddress($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 401);
            }
            $address = ShippingAddress::where('id', $id)->where('customer_id', $customerId)->first();
            if (!$address) {
                return ResponseHelper::error(null, 'Shipping address not found', 404);
            }
            $address->delete();
            return ResponseHelper::success(null, 'Shipping address deleted successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }

   public function salesReport(Request $request)
{
    try {
        $vendorId = auth()->id();
        if (!$vendorId) {
            return ResponseHelper::error(null, 'Unauthorized', 401);
        }
         $type = $request->get('type', 'overall');
         $report = $this->orderRepository->getSalesReport($vendorId, $type);
        
        return ResponseHelper::success($report,'Sales report fetched successfully',200);
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
            $order = $this->orderRepository->getOrderByIdAndCustomer($id, $customerId);
            if (!$order) {
                return ResponseHelper::error(null, 'Order not found', 404);
            }
            return ResponseHelper::success($order, 'Order details fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }

    public function getorderlist($orderId)
    {
        try {
            $orders = $this->orderRepository->getorderlist($orderId);
            return ResponseHelper::success($orders, 'Orders list fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 500);
        }
    }
}
