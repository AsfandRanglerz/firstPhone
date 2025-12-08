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
//         // ---------------------------
//         // BRAND & MODEL REQUIRED
//         // ---------------------------
//         if (!$request->filled('brand_id') || !$request->filled('model_id')) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Please enter required details first'
//             ], 400);
//         }

//         // ---------------------------
//         // BASE QUERY
//         // ---------------------------
//         $query = VendorMobile::with(['vendor', 'model'])
//             ->join('vendors', 'vendors.id', '=', 'vendor_mobiles.vendor_id');

//         // Mandatory filters (vendor_mobiles)
//         $query->where('vendor_mobiles.brand_id', $request->brand_id)
//               ->where('vendor_mobiles.model_id', $request->model_id);

//         // ---------------------------
//         // REPAIR SERVICE FILTER (vendor table)
//         // ---------------------------
        
//          $query->where('vendors.repair_service', $request->repair_service); // checkbox selected
        

//         // ---------------------------
//         // LOCATION FILTER LOGIC
//         // ---------------------------
//         $userLat = $request->latitude;
//         $userLng = $request->longitude;
//         $radius  = $request->radius;
//         $city    = $request->city;

//         $isRadiusMode = $request->filled('radius') && $request->filled('latitude') && $request->filled('longitude');
//         $isCityMode   = $request->filled('city');

//         if ($isRadiusMode) {
//             // RADIUS MODE → CITY IGNORE
//             $query->select('vendor_mobiles.*', 'vendors.latitude', 'vendors.longitude', 'vendors.location')
//                   ->selectRaw("
//                     (6371 * acos(
//                         cos(radians(?)) *
//                         cos(radians(vendors.latitude)) *
//                         cos(radians(vendors.longitude) - radians(?)) +
//                         sin(radians(?)) *
//                         sin(radians(vendors.latitude))
//                     )) AS distance
//                   ", [$userLat, $userLng, $userLat])
//                   ->having('distance', '<=', $radius)
//                   ->orderBy('distance');
//         } elseif ($isCityMode) {
//             // CITY MODE → RADIUS IGNORE
//             $query->where('vendors.location', $city);
//         }

//         // ---------------------------
//         // OPTIONAL FILTERS (vendor_mobiles)
//         // ---------------------------
//         if ($request->filled('storage'))   $query->where('vendor_mobiles.storage', $request->storage);
//         if ($request->filled('ram'))       $query->where('vendor_mobiles.ram', $request->ram);
//         if ($request->filled('condition')) $query->where('vendor_mobiles.condition', $request->condition);
//         if ($request->filled('color'))     $query->where('vendor_mobiles.color', $request->color);

//         // ---------------------------
//         // PRICE FILTERS
//         // ---------------------------
//         if ($request->filled('min_price') && $request->filled('max_price')) {
//             $query->whereBetween('vendor_mobiles.price', [$request->min_price, $request->max_price]);
//         } elseif ($request->filled('min_price')) {
//             $query->where('vendor_mobiles.price', '>=', $request->min_price);
//         } elseif ($request->filled('max_price')) {
//             $query->where('vendor_mobiles.price', '<=', $request->max_price);
//         }

//         // ---------------------------
//         // FETCH DATA
//         // ---------------------------
//         $listings = $query->get();

//         // EMPTY CHECK FOR RADIUS MODE
//         if ($isRadiusMode && $listings->isEmpty()) {
//             return response()->json([
//                 'status' => 'success',
//                 'data'   => []
//             ], 200);
//         }

//         // CITY MODE EMPTY CHECK
//         if (!$isRadiusMode && $listings->isEmpty()) {
//             $checkBase = VendorMobile::where('brand_id', $request->brand_id)
//                 ->where('model_id', $request->model_id)
//                 ->exists();

//             return response()->json([
//                 'status'  => 'not_found',
//                 'message' => $checkBase ? 'Mobile not found' : 'No Mobile Found'
//             ], 404);
//         }

//         // ---------------------------
//         // FORMAT OUTPUT
//         // ---------------------------
//         $response = $listings->map(function ($item) {
//             return [
//                 'image'          => $item->image,
//                 'title'          => $item->model->name ?? null,
//                 'price'          => $item->price,
//                 'shop_name'      => $item->shop_name,
//                 'location'       => $item->vendor->location ?? null,
//                 'latitude'       => $item->vendor->latitude ?? null,
//                 'longitude'      => $item->vendor->longitude ?? null,
//                 'condition'      => $item->condition,
//             ];
//         });

//         return response()->json([
//             'status' => 'success',
//             'data'   => $response
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

        // ---------------------------
        // REQUIRED FIELDS
        // ---------------------------
        if (!$request->filled('brand_id') || !$request->filled('model_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter required details first'
            ], 400);
        }

        // ---------------------------
        // BASE QUERY
        // ---------------------------
        $query = VendorMobile::with('vendor', 'model')
            ->where('brand_id', $request->brand_id)
            ->where('model_id', $request->model_id);

        // OPTIONAL FILTERS
        if ($request->filled('repair_service')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('repair_service', $request->repair_service);
            });
        }
        if ($request->filled('storage'))   $query->where('storage', $request->storage);
        if ($request->filled('ram'))       $query->where('ram', $request->ram);
        if ($request->filled('condition')) $query->where('condition', $request->condition);
        if ($request->filled('color'))     $query->where('color', $request->color);

        // ---------------------------
        // PRICE FILTER (LIKE)
        // ---------------------------
        $min = trim($request->min_price ?? '');
        $max = trim($request->max_price ?? '');

        if ($min !== '' && $max !== '') {
            if ($min > $max) [$min, $max] = [$max, $min];
            $query->where(function ($q) use ($min, $max) {
                $q->whereRaw("CAST(price AS CHAR) LIKE ?", ["%$min%"])
                  ->orWhereRaw("CAST(price AS CHAR) LIKE ?", ["%$max%"]);
            });
        } elseif ($min !== '') {
            $query->whereRaw("CAST(price AS CHAR) LIKE ?", ["%$min%"]);
        } elseif ($max !== '') {
            $query->whereRaw("CAST(price AS CHAR) LIKE ?", ["%$max%"]);
        }

        // ---------------------------
        // GET RESULTS FIRST
        // ---------------------------
        $listings = $query->get();

        // ---------------------------
        // LOCATION FILTERS
        // ---------------------------
        $cityMode   = $request->filled('city');
        $radiusMode = $request->filled('latitude') && $request->filled('longitude');

        $latReq  = $request->latitude;
        $lngReq  = $request->longitude;
        $radius  = $request->radius ?? 50;

        // PRIORITY
        // 1. If radius + city → use radius only
        // 2. If radius only → use radius
        // 3. If city only → use city

        if ($radiusMode) {

            // Radius filter only
            $listings = $listings->filter(function ($item) use ($latReq, $lngReq, $radius) {

                if (!$item->vendor?->latitude || !$item->vendor?->longitude) return false;

                $lat2 = $item->vendor->latitude;
                $lng2 = $item->vendor->longitude;

                // Haversine Formula
                $theta = $lngReq - $lng2;
                $dist = sin(deg2rad($latReq)) * sin(deg2rad($lat2)) +
                        cos(deg2rad($latReq)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $km   = $dist * 60 * 1.1515 * 1.609344;

                return $km <= $radius;
            })->values();

        } elseif ($cityMode) {

            // City filter only
            $city = strtolower($request->city);

            $listings = $listings->filter(function ($item) use ($city) {
                return strtolower($item->vendor->location ?? '') === $city;
            })->values();
        }

        // ---------------------------
        // FORMAT FOR RESPONSE
        // ---------------------------
        $formatted = $listings->map(function ($item) use ($radiusMode, $latReq, $lngReq) {

            // Calculate distance ONLY IF radius filter applied
            $distance = null;

            if ($radiusMode && $item->vendor?->latitude && $item->vendor?->longitude) {

                $theta = $lngReq - $item->vendor->longitude;
                $dist = sin(deg2rad($latReq)) * sin(deg2rad($item->vendor->latitude)) +
                        cos(deg2rad($latReq)) * cos(deg2rad($item->vendor->latitude)) * cos(deg2rad($theta));

                $dist = acos($dist);
                $dist = rad2deg($dist);
                $km   = $dist * 60 * 1.1515 * 1.609344;

                $distance = round($km, 2);
            }

            return [
                'image'     => $item->image,
                'title'     => $item->model->name ?? null,
                'price'     => $item->price,
                'shop_name' => $item->vendor->location ?? null,
                'latitude'  => $item->vendor->latitude ?? null,
                'longitude' => $item->vendor->longitude ?? null,
                'condition' => $item->condition,
                'distance'  => $distance
            ];
        });

        // ---------------------------
        // RETURN FINAL RESPONSE
        // ---------------------------
        return response()->json([
            'status' => 'success',
            'count'  => $formatted->count(),
            'data'   => $formatted
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}





}
