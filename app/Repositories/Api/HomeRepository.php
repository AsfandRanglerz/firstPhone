<?php

namespace App\Repositories\Api;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\Interfaces\HomeRepositoryInterface;

class HomeRepository implements HomeRepositoryInterface
{
    public function getHomeScreenData(Request $request)
    {
        $customer = Auth::id();
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        /**
         * NEARBY LISTINGS
         */
        $nearbyListingsQuery = MobileListing::with('model')
            ->where('status', 0)
            ->select('id', 'model_id', 'price', 'image', 'location')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($search) {
            $nearbyListingsQuery->where(function ($query) use ($search) {
                $query->whereHas('model', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->orWhere('location', 'LIKE', "%{$search}%")
                ->orWhere('storage', 'LIKE', "%{$search}%")
                ->orWhere('ram', 'LIKE', "%{$search}%");
            });
        }

        // Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $nearbyListingsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $nearbyListings = $nearbyListingsQuery
            ->take(6)
            ->get()
            ->map(function ($listing) {
                $images = json_decode($listing->image, true) ?? [];
                return [
                    'id'       => $listing->id,
                    'model'    => $listing->model ? $listing->model->name : null,
                    'price'    => $listing->price,
                    'location' => $listing->location,
                    'image'    => $images[0] ?? null,
                ];
            });

        /**
         * TOP SELLING LISTINGS
         */
        $topSellingQuery = OrderItem::with(['product.model', 'order'])
            ->whereHas('order', function ($query) {
                $query->where('order_status', 'delivered');
            });

        // Search filter
        if ($search) {
            $topSellingQuery->whereHas('product', function ($query) use ($search) {
                $query->whereHas('model', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->orWhere('location', 'LIKE', "%{$search}%")
                ->orWhere('storage', 'LIKE', "%{$search}%")
                ->orWhere('ram', 'LIKE', "%{$search}%");
            });
        }

        // Date filter
        if (!empty($startDate) && !empty($endDate)) {
            $topSellingQuery->whereHas('product', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $topSelling = $topSellingQuery
            ->get()
            ->groupBy('product_id')
            ->sortByDesc(function ($items) {
                return $items->count();
            })
            ->take(6)
            ->map(function ($items) {
                $product = $items->first()->product;
                $images = json_decode($product->image, true) ?? [];
                return [
                    'id'       => $product->id,
                    'model'    => $product->model ? $product->model->name : null,
                    'price'    => $product->price,
                    'location' => $product->location,
                    'image'    => $images[0] ?? null,
                ];
            })
            ->values();

        return [
            'nearby_listings' => $nearbyListings,
            'top_selling'     => $topSelling
        ];
    }
}
