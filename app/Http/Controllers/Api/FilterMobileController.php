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
        $brandId = $request->brand_id;

        $models = DB::table('mobile_listings as ml')
            ->join('models as m', 'ml.model_id', '=', 'm.id')
            ->where('ml.status', 2)
            ->where('ml.brand_id', $brandId)
            ->select('m.id', 'm.name as model_name')
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
   public function getBrandsByModel($modelId)
{
    $brands = DB::table('mobile_listings as ml')
        ->join('brands as b', 'ml.brand_id', '=', 'b.id')
        ->where('ml.model_id', $modelId)
        ->where('ml.status', 1)
        ->select('b.id', 'b.name as brand_name')
        ->distinct()
        ->get();

    return response()->json($brands);
}


    // Step 3: Get Full Data by Brand & Model
   public function getDataByBrandModel(Request $request)
{
    $modelId = $request->input('model_id');
    $brandId = $request->input('brand_id');

    $data = DB::table('mobile_listings as ml')
        ->join('brands as b', 'ml.brand_id', '=', 'b.id')
        ->join('models as m', 'ml.model_id', '=', 'm.id')
        ->where('ml.model_id', $modelId)
        ->where('ml.brand_id', $brandId)
        ->where('ml.status', 1)
        ->select(
            'ml.id',
            'm.name as model',
            'b.name as brand',
            'ml.storage',
            'ml.ram',
            'ml.price',
            'ml.condition',
            'ml.image',
            'ml.about',
            'ml.status',
            'ml.action',
            'ml.color',
            'ml.repairing_service'
        )
        ->get();

    return response()->json($data);
}

}