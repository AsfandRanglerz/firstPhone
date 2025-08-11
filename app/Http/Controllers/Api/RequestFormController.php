<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

            return response()->json([
                'message' => 'Mobile request submitted successfully',
                'data'    => $mobileRequest
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
