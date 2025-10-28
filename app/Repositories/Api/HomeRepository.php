<?php

namespace App\Repositories\Api;

use App\Models\OrderItem;
use App\Models\VendorMobile;
use Illuminate\Http\Request;
use App\Models\MobileListing;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\Interfaces\HomeRepositoryInterface;

class HomeRepository implements HomeRepositoryInterface
{
    public function getNearbyListings($request)
    {
        $customerLat = $request->query('latitude');
        $customerLng = $request->query('longitude');

        if (!$customerLat || !$customerLng) {
            throw new \Exception('Latitude and Longitude are required to fetch nearby listings');
        }

        $radius = $request->query('radius', 30);
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = VendorMobile::with(['model', 'vendor'])
            ->join('vendors', 'vendor_mobiles.vendor_id', '=', 'vendors.id')
            ->select(
                'vendor_mobiles.id',
                'vendor_mobiles.vendor_id',
                'vendor_mobiles.model_id',
                'vendor_mobiles.price',
                'vendor_mobiles.image',
                'vendor_mobiles.stock',
                'vendor_mobiles.location',
                'vendors.latitude',
                'vendors.longitude',
            )
            ->selectRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(vendors.latitude)) *
                    cos(radians(vendors.longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(vendors.latitude))
                )) AS distance
            ", [$customerLat, $customerLng, $customerLat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->where('vendor_mobiles.stock', '>', 0);

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('vendor_mobiles.price', 'LIKE', "%{$search}%")
                ->orWhere('vendor_mobiles.location', 'LIKE', "%{$search}%")
                ->orWhere('vendor_mobiles.storage', 'LIKE', "%{$search}%")
                ->orWhere('vendor_mobiles.ram', 'LIKE', "%{$search}%")
                ->orWhereHas('model', fn($m) => $m->where('name', 'LIKE', "%{$search}%"));
            });
        }

        // Date filter (optional)
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('vendor_mobiles.created_at', [$startDate, $endDate]);
        }

        // Fetch + map
        $listings = $query->take(6)->get()->map(function ($listing) {
            $images = json_decode($listing->image, true) ?? [];
            return [
                'id'       => $listing->id,
                'vendor'   => $listing->vendor?->name,
                'model'    => $listing->model?->name,
                'price'    => $listing->price,
                'distance' => round($listing->distance, 1) . ' km',
                'image'    => isset($images[0]) ? asset($images[0]) : null,
            ];
        });

        return $listings;
    }

    public function getTopSellingListings($request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Base query - sirf delivered orders ka data
        $query = OrderItem::with(['product.model', 'order'])
            ->whereHas('order', fn($q) => $q->where('order_status', 'delivered'))
            ->whereHas('product', fn($q) => $q->where('stock', '>', 0)); // exclude out-of-stock listings

        // Search filter (model name, price, etc.)
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('price', 'LIKE', "%{$search}%")
                ->orWhere('location', 'LIKE', "%{$search}%")
                ->orWhere('storage', 'LIKE', "%{$search}%")
                ->orWhere('ram', 'LIKE', "%{$search}%")
                ->orWhereHas('model', fn($m) => $m->where('name', 'LIKE', "%{$search}%"));
            });
        }

        // Date filter (product creation date)
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereHas('product', fn($q) =>
                $q->whereBetween('created_at', [$startDate, $endDate])
            );
        }

        // Group by product & sort by number of sales
        $topSelling = $query->get()
            ->groupBy('product_id')
            ->sortByDesc(fn($items) => $items->count())
            ->take(6)
            ->map(function ($items) {
                $product = $items->first()->product;


                if (!$product) return null; 

                $images = json_decode($product->image, true) ?? [];

                return [
                    'id'       => $product->id,
                    'model'    => $product->model?->name,
                    'price'    => $product->price,
                    'image'    => isset($images[0]) ? asset($images[0]) : null,
                    'total_sales' => $items->count(), // optional: sales count
                ];
            })
            ->filter()
            ->values();

        return $topSelling;
    }

    public function getDeviceDetails($id)
    {
        $listing = VendorMobile::with(['brand', 'model'])
            ->where('id', $id)
            ->firstOrFail();

        $images = json_decode($listing->image, true) ?? [];

        return [
            'id'              => $listing->id,
            'brand'           => $listing->brand ? $listing->brand->name : null,
            'model'           => $listing->model ? $listing->model->name : null,
            'storage'         => $listing->storage,
            'price'           => $listing->price,
            'condition'       => $listing->condition,
            'color'           => $listing->color,
            'ram'             => $listing->ram,
            'processor'       => $listing->processor,
            'display'         => $listing->display,
            'charging'        => $listing->charging,
            'refresh_rate'    => $listing->refresh_rate,
            'main_camera'     => $listing->main_camera,
            'ultra_camera'    => $listing->ultra_camera,
            'telephoto_camera'=> $listing->telephoto_camera,
            'front_camera'    => $listing->front_camera,
            'build'           => $listing->build,
            'wireless'        => $listing->wireless,
            'stock'           => $listing->stock,
            'ai_features'     => $listing->ai_features,
            'battery_health'  => $listing->battery_health,
            'os_version'      => $listing->os_version,
            'warranty_start'  => $listing->warranty_start, 
            'warranty_end'    => $listing->warranty_end,
            'pta_approved'    => $listing->pta_approved == 0 ? 'Approved' : 'Not Approved',
            'images'          => array_map(function ($path) {
                return asset($path);
            }, $images),
        ];
    }
}
    
