<?php

namespace App\Repositories\Api\Interfaces;

use Illuminate\Support\Collection;
use App\Models\Order; // ✅ correct model namespace

interface OrderRepositoryInterface
{
    /**
     * Get orders for a customer filtered by status
     *
     * @param int $customerId
     * @param string $status
     * @return Collection
     */
    public function getOrdersByCustomerAndStatus(int $customerId, string $status): Collection;

    /**
     * Get a single order with relations by order id and customer id
     *
     * @param int $orderId
     * @param int $customerId
     * @return Order
     */
    public function getOrderWithRelations(int $orderId, int $customerId): Order;

    /**
     * Get an order by id and customer id
     *
     * @param int $orderId
     * @param int $customerId
     * @return Order
     */
    public function getOrderByIdAndCustomer(int $orderId, int $customerId): Order;

    public function getOrderByIdAndVendor(int $orderId, int $customerId): Order;


    public function getSalesReport(int $vendorId): array;

    public function getOrderStatistics(int $vendorId): array;

    public function createDeviceReceipts(int $orderId, array $devices): array;

    public function getReceiptById(int $deviceReceiptId): array;

    public function getOrdersByVendorAndStatus(int $vendorId, string $status): Collection;

    public function getVendorOrderDetails(int $vendorId, int $orderId): array;

    public function reOrder(int $orderId, int $customerId);

}
