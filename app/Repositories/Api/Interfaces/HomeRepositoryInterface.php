<?php

namespace App\Repositories\Api\Interfaces;

use Illuminate\Http\Request;

interface HomeRepositoryInterface
{
    // public function getHomeScreenData(Request $request);
    public function getDeviceDetails($id);
    public function getNearbyListings(Request $request);
    public function getTopSellingListings(Request $request);
}
