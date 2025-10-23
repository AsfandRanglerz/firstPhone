<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

            if (!empty($data['error']) && $data['error'] === true) {
                return ResponseHelper::error(
                    $data['message'] ?? 'Something went wrong',
                    $data['message'] ?? 'Duplicate listing',
                    'already_exists',
                    409
                );
            }

            // Success case
            return ResponseHelper::success($data, 'Customer listing created successfully', null, 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), 'Validation failed', 'validation_error', 422);

        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), '1062')) {
                return ResponseHelper::error('Listing already exists', 'Listing already exists', 'already_exists', 409);
            }
            return ResponseHelper::error($e->getMessage(), 'Database error', 'server_error', 500);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::error('Resource not found', 'Resource not found', 'not_found', 404);

        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Failed to create customer listing', 'server_error', 500);
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

    public function getcustomermobileListing()
{
    try{
        $listings = $this->mobileListingService->getcustomermobileListing();
        return ResponseHelper::success($listings, 'Mobile listings retrieved successfully', null, 200);

    }  catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving the listing', 'error', 500);
    }
}



}
