<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileRequest;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Repositories\Api\Interfaces\RequestedMobileRepositoryInterface;

class RequestFormController extends Controller
{
    protected $requestedMobileRepository;
    
    public function __construct(RequestedMobileRepositoryInterface $requestedMobileRepository)
    {
        $this->requestedMobileRepository = $requestedMobileRepository;
    }

     public function mobilerequestform(Request $request)
    {
        try {
            
            $user = Auth::user();
            // Create request
            $mobileRequest = MobileRequest::create([
                'name'            => $user->name,
                'location'        => $request->location,
                'brand_id'        => $request->brand_id,
                'model_id'        => $request->model_id,
                'min_price'      => $request->min_price,
                'max_price'      => $request->max_price,
                'storage'         => $request->storage,
                'ram'             => $request->ram,
                'color'           => $request->color,
                'condition'       => $request->condition,
                'description'     => $request->description,
            ]);

    return ResponseHelper::success($mobileRequest, 'Mobile request submitted successfully', null, 200);

    } catch (ValidationException $e) {
        return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while submitting the request', 'error', 500);
    }
    }

    public function getRequestedMobile()
{
    try {
    $mobileRequests = $this->requestedMobileRepository->getRequestedMobile();
    return ResponseHelper::success($mobileRequests, 'Requests fetched successfully', null, 200);
    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while fetching requests', 'error', 500);
    }
}


}
