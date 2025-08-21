<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\MobileListing;
use App\Models\MobileModel;
use Illuminate\Http\Request;

class MobileFilterController extends Controller
{
    //
    public function getBrands()
    {
        $brands = Brand::all();

        return response()->json([
            'data'   => $brands
        ]);
    }

    /**
     * Get models by brand_id
     */
    public function getModels($brand_id)
    {
        $models = MobileModel::where('brand_id', $brand_id)->get();

        if ($models->isEmpty()) {
            return response()->json([
                'message' => 'No Models Found For This Brand'
            ], 404);
        }

        return response()->json([
            'data'   => $models
        ]);
    }

    /**
     * Get filtered listings by brand, model, storage, ram, etc.
     */
    public function getData(Request $request)
    {
        try {
            $query = MobileListing::query();

            if ($request->brand_id) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->model_id) {
                $query->where('model_id', $request->model_id);
            }

            if ($request->storage) {
                $query->where('storage', $request->storage);
            }

            if ($request->ram) {
                $query->where('ram', $request->ram);
            }

            if ($request->condition) {
                $query->where('condition', $request->condition);
            }

            if ($request->color) {
                $query->where('color', $request->color);
            }

            $query->when($request->latitude, function ($q, $latitude) {
                return $q->where('latitude', $latitude);
            });

            $query->when($request->longitude, function ($q, $longitude) {
                return $q->where('longitude', $longitude);
            });


            if ($request->city) {
                $query->where('location', $request->city);
            }

            // ✅ Price filter
            if ($request->min_price && $request->max_price) {
                $query->whereBetween('price', [$request->min_price, $request->max_price]);
            }

            // ✅ Only approved listings
            $listings = $query->where('status', 0)->get();

            // ✅ Check if no data found
            if ($listings->isEmpty()) {
                return response()->json([
                    'message' => 'No Data Found For The Given Filters'
                ], 404);
            }

            return response()->json([
                'data'   => $listings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
