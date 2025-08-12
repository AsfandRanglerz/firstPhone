<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileRequest;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RequestFormController extends Controller
{
     public function mobilerequestform(Request $request)
    {
        try {
            
            $user = Auth::user();
            // Create request
            $mobileRequest = MobileRequest::create([
                'name'            => $user->name,
                'location'        => $request->location,
                'brand'            => $request->brand,
                'model'            => $request->model,
                'storage'          => $request->storage,
                'ram'              => $request->ram,
                'color'            => $request->color,
                'condition'        => $request->condition,
            ]);

    return ResponseHelper::success($mobileRequest, 'Mobile request submitted successfully', null, 200);

    } catch (ValidationException $e) {
        return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while submitting the request', 'error', 500);
    }
    }

}
