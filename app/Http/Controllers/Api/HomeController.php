<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function homeScreen(Request $request)
    {
        try {
            $customer = Auth::id();
            // NEARBY LISTINGS
            $nearbyListings = MobileListing::with('model') 
                ->select('id', 'model_id', 'price', 'image', 'location')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get()
                ->map(function ($listing) {
                      $images = json_decode($listing->image, true) ?? [];
                    return [
                        'id' => $listing->id,
                        'model' => $listing->model ? $listing->model->name : null,
                        'price' => $listing->price,
                        'location' => $listing->location,
                        'image' => $images[0] ?? null,
                    ];
                });
            // TOP SELLING
            // $topSelling = Order::select(
            //         'mobile_listings.id',
            //         'mobile_listings.model_id',
            //         'mobile_listings.price',
            //         'mobile_listings.image',
            //         'mobile_listings.location',
            //         DB::raw('COUNT(orders.id) as total_sold')
            //     )
            //     ->join('mobile_listings', 'orders.mobile_listing_id', '=', 'mobile_listings.id')
            //     ->groupBy('mobile_listings.id', 'mobile_listings.model_id', 'mobile_listings.price', 'mobile_listings.image', 'mobile_listings.location')
            //     ->orderBy('total_sold', 'desc')
            //     ->take(6)
            //     ->get()
            //     ->map(function ($item) {
            //         return [
            //             'id' => $item->id,
            //             'model' => MobileModel::find($item->model_id)->name ?? null,
            //             'price' => $item->price,
            //             'location' => $item->location,
            //             'image' => json_decode($item->image, true) ?? [],
            //             'total_sold' => $item->total_sold,
            //         ];
            //     });

            $data = [
            'nearby' => $nearbyListings,
            // 'top_selling' => $topSelling
        ];

        return ResponseHelper::success($data, 'Home screen data retrieved successfully', null, 200);

    } catch (Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving home screen data', 'error', 500);
    }
    }

}
