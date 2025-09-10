<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileRequest;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Vendor;
use App\Helpers\NotificationHelper;
use App\Repositories\Api\Interfaces\RequestedMobileRepositoryInterface;
use Illuminate\Support\Facades\DB;

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

            // Save mobile request
            $mobileRequest = MobileRequest::create([
                'name'        => $user->name,
                'location'    => $request->location,
                'brand_id'    => $request->brand_id,
                'model_id'    => $request->model_id,
                'min_price'   => $request->min_price,
                'max_price'   => $request->max_price,
                'storage'     => $request->storage,
                'ram'         => $request->ram,
                'color'       => $request->color,
                'condition'   => $request->condition,
                'description' => $request->description,
            ]);

            // Customer location + radius
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->location ?? 10; //

            // Vendors filter by brand/model & distance
            $vendors = Vendor::whereHas('mobileListings', function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id)
                    ->where('model_id', $request->model_id);
            })
                ->select('*', DB::raw("6371 * acos(cos(radians($lat)) 
                        * cos(radians(latitude)) 
                        * cos(radians(longitude) - radians($lng)) 
                        + sin(radians($lat)) 
                        * sin(radians(latitude))) AS distance"))
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->get();

            // Send notifications
            foreach ($vendors as $vendor) {
                if (!empty($vendor->fcm_token)) {
                    NotificationHelper::sendFcmNotification(
                        $vendor->fcm_token,
                        "New Mobile Request",
                        "Customer requested {$mobileRequest->brand->name} {$mobileRequest->model->name}",
                        [
                            'request_id' => (string) $mobileRequest->id,
                            'min_price'  => (string) $mobileRequest->min_price,
                            'max_price'  => (string) $mobileRequest->max_price,
                            'distance'   => (string) round($vendor->distance, 2) . " km"
                        ]
                    );
                }
            }

            return ResponseHelper::success(
                $mobileRequest,
                "Mobile request submitted successfully & vendors notified (within {$radius} km)",
                null,
                200
            );
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
