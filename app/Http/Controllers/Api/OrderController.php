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
                return ResponseHelper::error(null, 'Not Permission', 'unauthorized');
            }
            if (!$status) {
                return ResponseHelper::error(null, 'Status parameter is required', 'error');
            }
            $orders = $this->orderRepository->getOrdersByCustomerAndStatus($customerId, $status);
            if ($orders->isEmpty()) {
                return ResponseHelper::error([], "No orders found for status: {$status}", 'not_found');
            }

            return ResponseHelper::success($orders, 'Orders fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
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
            ], 'Order statistics fetched successfully', 'success');

        } catch (\InvalidArgumentException $e) {
            // ✅ Invalid date format (user input issue)
            return ResponseHelper::error(null, $e->getMessage(), 'bad_request');
        } catch (\Exception $e) {
            // ❌ All other system errors
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }




    public function track($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Not Permission', 'unauthorized');
            }

            $order = $this->orderRepository->getOrderByIdAndCustomer($id, $customerId);

            return ResponseHelper::success([
                'order_id' => $order->id,
                'status'   => $order->order_status,
            ], 'Order status fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function trackVendor($id)
    {
        try {
            $vendorId = auth()->id();
            if (!$vendorId) {
                return ResponseHelper::error(null, 'Unauthorized', 'unauthorized');
            }

            $order = $this->orderRepository->getOrderByIdAndVendor($id, $vendorId);

            return ResponseHelper::success([
                'order_id' => $order->id,
                'status'   => $order->order_status,
            ], 'Order status fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
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
                return ResponseHelper::error(null, 'Unauthorized', 'unauthorized');
            }
            $address = ShippingAddress::where('customer_id', $customerId)->latest()->first();
            if (!$address) {
                return ResponseHelper::error(null, 'No shipping address found', 'not_found');
            }
            return ResponseHelper::success($address, 'Shipping address fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function deleteShippingAddress($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 'unauthorized');
            }
            $address = ShippingAddress::where('id', $id)->where('customer_id', $customerId)->first();
            if (!$address) {
                return ResponseHelper::error(null, 'Shipping address not found', 'not_found');
            }
            $address->delete();
            return ResponseHelper::success(null, 'Shipping address deleted successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function salesReport(Request $request)
    {
        try {
            $vendorId = auth()->id();
            if (!$vendorId) {
                return ResponseHelper::error(null, 'Unauthorized', 'unauthorized');
            }
            $type = $request->get('type', 'overall');
            $report = $this->orderRepository->getSalesReport($vendorId, $type);

            return ResponseHelper::success($report, 'Sales report fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }


    public function show($id)
    {
        try {
            $customerId = auth()->id();
            if (!$customerId) {
                return ResponseHelper::error(null, 'Unauthorized', 'unauthorized');
            }
            $order = $this->orderRepository->getOrderByIdAndCustomer($id, $customerId);
            if (!$order) {
                return ResponseHelper::error(null, 'Order not found', 'not_found');
            }
            return ResponseHelper::success($order, 'Order details fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function getorderlist($orderId)
    {
        try {
            $orders = $this->orderRepository->getorderlist($orderId);
            return ResponseHelper::success($orders, 'Orders list fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function deviceReceipt(Request $request, $orderId)
    {
        try {
            $devices = $request->input('devices', []);

            $receipts = $this->orderRepository->createDeviceReceipts($orderId, $devices);

            return ResponseHelper::success($receipts, 'Device receipt created successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }

    public function getReceipt($deviceReceiptId)
    {
        try {
            $receipt = $this->orderRepository->getReceiptById($deviceReceiptId);

            return ResponseHelper::success($receipt, 'Receipt fetched successfully', 'success');
        } catch (\Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 'server_error');
        }
    }
}
