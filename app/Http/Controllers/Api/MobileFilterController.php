<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use App\Models\MobileModel;
use App\Models\VendorMobile;
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
                    200
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
    // public function getData(Request $request)
    // {
    //     try {
    //         $query = VendorMobile::query();

    //         // ✅ Optional brand filter
    //         if ($request->filled('brand_id')) {
    //             $query->where('brand_id', $request->brand_id);
    //         }

    //         // ✅ Optional model filter
    //         if ($request->filled('model_id')) {
    //             $query->where('model_id', $request->model_id);
    //         }

    //         // ✅ Optional storage filter
    //         if ($request->filled('storage')) {
    //             $query->where('storage', $request->storage);
    //         }

    //         // ✅ Optional ram filter
    //         if ($request->filled('ram')) {
    //             $query->where('ram', $request->ram);
    //         }

    //         // ✅ Optional condition filter
    //         if ($request->filled('condition')) {
    //             $query->where('condition', $request->condition);
    //         }

    //         // ✅ Optional color filter
    //         if ($request->filled('color')) {
    //             $query->where('color', $request->color);
    //         }

    //         // ✅ Optional latitude filter
    //         if ($request->filled('latitude')) {
    //             $query->where('latitude', $request->latitude);
    //         }

    //         // ✅ Optional longitude filter
    //         if ($request->filled('longitude')) {
    //             $query->where('longitude', $request->longitude);
    //         }

    //         // ✅ Optional city / location filter
    //         if ($request->filled('city')) {
    //             $query->where('location', $request->city);
    //         }

    //         // ✅ Optional price range filter
    //         if ($request->filled('min_price') && $request->filled('max_price')) {
    //             $query->whereBetween('price', [$request->min_price, $request->max_price]);
    //         } elseif ($request->filled('min_price')) {
    //             $query->where('price', '>=', $request->min_price);
    //         } elseif ($request->filled('max_price')) {
    //             $query->where('price', '<=', $request->max_price);
    //         }

            
    //         $listings = $query->get();

    //         // ✅ Check if no data found
    //         if ($listings->isEmpty()) {
    //             return response()->json([
    //                 'status' => 'not_found',
    //                 'message' => 'No Data Found For The Given Filters'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'data' => $listings
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

	public function getData(Request $request)
{
    try {
        // Brand and model required
        if (!$request->filled('brand_id') || !$request->filled('model_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter required details first'
            ], 400);
        }

        $query = VendorMobile::query();

        // Mandatory filters
        $query->where('brand_id', $request->brand_id)
              ->where('model_id', $request->model_id);

        // Optional filters
        if ($request->filled('storage')) $query->where('storage', $request->storage);
        if ($request->filled('ram')) $query->where('ram', $request->ram);
        if ($request->filled('condition')) $query->where('condition', $request->condition);
        if ($request->filled('color')) $query->where('color', $request->color);
        if ($request->filled('latitude')) $query->where('latitude', $request->latitude);
        if ($request->filled('longitude')) $query->where('longitude', $request->longitude);
        if ($request->filled('city')) $query->where('location', $request->city);

        // Price filters
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        } elseif ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Fetch Data
        $listings = $query->get();

        // If model & brand exist but filters mismatch
        if ($listings->isEmpty()) {
            
            // Check if brand & model exist without extra filters
            $checkOnlyBrandModel = VendorMobile::where('brand_id', $request->brand_id)
                ->where('model_id', $request->model_id)
                ->exists();

            if ($checkOnlyBrandModel) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Mobile not found'
                ], 404);
            }

            return response()->json([
                'status' => 'not_found',
                'message' => 'No Mobile Found'
            ], 404);
        }


        // Return only selected fields
        $response = $listings->map(function ($item) {
            return [
                'image'     => $item->image,
                'title'     => $item->title,
                'price'     => $item->price,
                'shop_name' => $item->shop_name,
                'location'  => $item->location,
                'condition' => $item->condition,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $response
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}


}
