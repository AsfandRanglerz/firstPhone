<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Api\VendorMobileListingService;

class VendorMobileListingController extends Controller
{
    protected $vendormobileListingService;

    public function __construct(VendorMobileListingService $vendormobileListingService)
    {
        $this->vendormobileListingService = $vendormobileListingService;
    }

    public function mobileListing(Request $request)
    {
        try {
            $data = $this->vendormobileListingService->createListing($request);
            return ResponseHelper::success($data, 'Listing added successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while creating the listing', 'error', 500);
        }
    }

    public function previewListing($id)
    {
        try {
            $data = $this->vendormobileListingService->previewListing($id);
            return ResponseHelper::success($data, 'Preview generated successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while generating preview', 'error', 500);
        }
    }
}
