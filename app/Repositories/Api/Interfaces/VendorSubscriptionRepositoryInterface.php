<?php

namespace App\Repositories\Api\Interfaces;

use Illuminate\Http\Request;

interface VendorSubscriptionRepositoryInterface
{
    public function subscribe(Request $request);
    public function current(Request $request);
}
