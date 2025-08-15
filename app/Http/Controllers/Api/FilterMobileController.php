<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterMobileController extends Controller
{
    // Step 1: Get Models
    public function getModels(Request $request)
    {
        try {
            $brand = $request->brand;
    
            $models = DB::table('mobile_listings')
                ->where('status', 2)
                ->where('brand', $brand)
                ->select('model')
                ->distinct()
                ->get();
    
            return response()->json([
                'status' => 'success',
                'data' => $models
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    // Step 2: Get Brands by Model
    public function getBrandsByModel($model)
    {
        $brands = DB::select("SELECT DISTINCT brand FROM mobile_listings WHERE model = ? AND status = 1", [$model]);
        return response()->json($brands);
    }

    // Step 3: Get Full Data by Brand & Model
    public function getDataByBrandModel(Request $request)
    {
        $model = $request->input('model');
        $brand = $request->input('brand');

        $data = DB::select("
            SELECT id, model, brand, storage, ram, price, `condition`, image, about, status, action, color, repairing_service
            FROM mobile_listings
            WHERE model = ? AND brand = ? AND status = 1
        ", [$model, $brand]);

        return response()->json($data);
    }
}

