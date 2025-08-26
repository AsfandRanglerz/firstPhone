<?php

namespace App\Repositories\Api;

use App\Models\MobileRequest;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\Interfaces\RequestedMobileRepositoryInterface;

class RequestedMobileRepository implements RequestedMobileRepositoryInterface
{
    public function getRequestedMobile()
    {
        $user = Auth::user();

        $vendorLocation = $user->location;

        if (!$vendorLocation) {
            return ResponseHelper::error(null, 'Vendor location not found', 'error', 400);
        }

        // Fetch requests that match vendor location
        $mobileRequests = MobileRequest::where('location', $vendorLocation)
            ->with(['brand:id,name', 'model:id,name']) 
            ->get()
            ->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'brand_model' => $item->brand?->name . ' ' . $item->model?->name,
            'location' => $item->location,
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            'min_price' => $item->min_price,
            'max_price' => $item->max_price,
            'storage' => $item->storage,
            'ram' => $item->ram,
            'color' => $item->color,
            'condition' => $item->condition,
            'description' => $item->description,
            'status' => $item->status,
        ];
    });
        return $mobileRequests;
    }
}
