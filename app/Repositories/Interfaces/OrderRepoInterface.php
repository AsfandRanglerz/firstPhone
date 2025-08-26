<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface OrderRepoInterface
{
    public function getAllOrders();
    public function deleteOrder($id);
    public function updateOrderStatus(Request $request, $id);
    public function updatePaymentStatus(Request $request, $id);
    public function pendingOrdersCount();
    public function getTotals();
}
