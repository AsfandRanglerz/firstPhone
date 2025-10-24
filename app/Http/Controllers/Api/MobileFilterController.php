<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use App\Models\MobileModel;
use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;

class MobileFilterController extends Controller
{
     /**
     * Get all brands
     */
    public function getBrands()
    {
        try {
            $brands = Brand::all();

            return ResponseHelper::success(
                $brands,
                'Brands fetched successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Error fetching brands: ' . $e->getMessage());

            return ResponseHelper::error(
                null,
                'Failed to fetch brands',
                'server_error',
                500
            );
        }
    }

    /**
     * Get models by brand_id
     */
    public function getModels($brand_id)
    {
        try {
            $models = MobileModel::where('brand_id', $brand_id)->get();

            if ($models->isEmpty()) {
                return ResponseHelper::error(
                    null,
                    'No Models Found For This Brand',
                    'not_found',
                    404
                );
            }

            return ResponseHelper::success(
                $models,
                'Mobile models fetched successfully'
            );

        } catch (\Exception $e) {
            \Log::error('Error fetching models for brand ID ' . $brand_id . ': ' . $e->getMessage());

            return ResponseHelper::error(
                null,
                'Failed to fetch mobile models',
                'server_error',
                500
            );
        }
    }

    /**
     * Get filtered listings by brand, model, storage, ram, etc.
     */
    public function getData(Request $request)
    {
        try {
            $query = MobileListing::query();

            // ✅ Optional brand filter
            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            // ✅ Optional model filter
            if ($request->filled('model_id')) {
                $query->where('model_id', $request->model_id);
            }

            // ✅ Optional storage filter
            if ($request->filled('storage')) {
                $query->where('storage', $request->storage);
            }

            // ✅ Optional ram filter
            if ($request->filled('ram')) {
                $query->where('ram', $request->ram);
            }

            // ✅ Optional condition filter
            if ($request->filled('condition')) {
                $query->where('condition', $request->condition);
            }

            // ✅ Optional color filter
            if ($request->filled('color')) {
                $query->where('color', $request->color);
            }

            // ✅ Optional latitude filter
            if ($request->filled('latitude')) {
                $query->where('latitude', $request->latitude);
            }

            // ✅ Optional longitude filter
            if ($request->filled('longitude')) {
                $query->where('longitude', $request->longitude);
            }

            // ✅ Optional city / location filter
            if ($request->filled('city')) {
                $query->where('location', $request->city);
            }

            // ✅ Optional price range filter
            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->whereBetween('price', [$request->min_price, $request->max_price]);
            } elseif ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            } elseif ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // ✅ Only approved listings
            $listings = $query->where('status', 0)->get();

            // ✅ Check if no data found
            if ($listings->isEmpty()) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'No Data Found For The Given Filters'
                ], 404);
            }

            return response()->json([
                'data' => $listings
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
