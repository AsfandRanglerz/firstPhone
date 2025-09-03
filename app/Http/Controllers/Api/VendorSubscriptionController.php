<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Api\Interfaces\VendorSubscriptionRepositoryInterface;

class VendorSubscriptionController extends Controller
{
    protected $vendorSubscriptionRepo;

    public function __construct(VendorSubscriptionRepositoryInterface $vendorSubscriptionRepo)
    {
        $this->vendorSubscriptionRepo = $vendorSubscriptionRepo;
    }

    public function subscribe(Request $request)
    {
        return $this->vendorSubscriptionRepo->subscribe($request);
    }

    public function current(Request $request)
    {
        return $this->vendorSubscriptionRepo->current($request);
    }
}
