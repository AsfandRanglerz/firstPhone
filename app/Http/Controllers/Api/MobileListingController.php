<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Api\MobileListingService;
use Illuminate\Validation\ValidationException;

class MobileListingController extends Controller
{
   protected $mobileListingService;

    public function __construct(MobileListingService $mobileListingService)
    {
        $this->mobileListingService = $mobileListingService;
    }

    public function mobileListing(Request $request)
    {
        try {
            $data = $this->mobileListingService->createListing($request);
            return ResponseHelper::success($data, 'Listing added successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while creating the listing', 'error', 500);
        }
    }

    public function previewListing($id)
    {
        try {
            $data = $this->mobileListingService->previewListing($id);
            return ResponseHelper::success($data, 'Preview generated successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while generating preview', 'error', 500);
        }
    }


public function getmobileListing()
{
    try{
        $vendor = Auth::id();
        $listings = MobileListing::with('model')
            ->where('vendor_id', $vendor)
            ->get()
            ->map(function ($listing) {
                return [
                    'model' => $listing->model ? $listing->model->name : null,
                    'price' => $listing->price,
                    'image' => $listing->image ? array_map(function ($path) {
                        return asset($path);
                    }, json_decode($listing->image, true) ?? []) : [],
                    'status' => $listing->status,
                ];
            });
        $data = $listings->count() === 1 ? $listings->first() : $listings;
        return ResponseHelper::success($listings, 'Mobile listings retrieved successfully', null, 200);

    } catch (ValidationException $e) {
        return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving the listing', 'error', 500);
    }


}
}
