<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Api\MobileListingService;

class CustomerMobileListingController extends Controller
{
    protected $mobileListingService;
    public function __construct(MobileListingService $mobileListingService)
    {
        $this->mobileListingService = $mobileListingService;
    }  

    public function customermobileListing(Request $request)
    {
        try {
            $data = $this->mobileListingService->createCustomerListing($request);
            return ResponseHelper::success($data, 'Customer listing created successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Failed to create customer listing', 'error', 500);
        }
    }

    public function previewCustomerListing($id)
    {
        try {
            $data = $this->mobileListingService->previewCustomerListing($id);
            return ResponseHelper::success($data, 'Preview generated successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while generating preview', 'error', 500);
        }
    }

}
