<?php

namespace App\Services\Api;

use Carbon\Carbon;
use App\Models\MobileListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\MobileListingRepository;

class MobileListingService
{
    protected $mobileListingRepo;

    public function __construct(MobileListingRepository $mobileListingRepo)
    {
        $this->mobileListingRepo = $mobileListingRepo;
    }

    

public function createCustomerListing($request)
{
    $customerId = Auth::id();

    // 🔍 Pehle check karo ke ye mobile pehle se listed to nahi
    $alreadyExists = MobileListing::where('customer_id', $customerId)
        ->where('brand', $request->brand)
        ->where('model', $request->model)
        ->exists();

if ($alreadyExists) {
        // Instead of throwing exception, bas error message return kar do
        return [
            'error' => true,
            'message' => 'You have already listed this mobile model.'
        ];
    }

    // ✅ Handle media upload
    $mediaPaths = [];
    if ($request->hasFile('image')) {
        foreach ($request->file('image') as $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('admin/assets/images/users/'), $filename);
            $mediaPaths[] = 'public/admin/assets/images/users/' . $filename;
        }
    }

    $data = [
        'brand'    => $request->brand,
        'model'    => $request->model,
        'location'    => $request->location,
        'latitude'    => $request->latitude,
        'longitude'   => $request->longitude,
        'storage'     => $request->storage,
        'ram'         => $request->ram,
        'price'       => $request->price,
        'condition'   => $request->condition,
        'about'       => $request->about,
        'customer_id' => $customerId,
        'image'       => json_encode($mediaPaths),
    ];

    $listing = $this->mobileListingRepo->create($data);

    $data['id'] = $listing->id;
    $data['image'] = array_map(fn($path) => asset($path), $mediaPaths);

    return $data;
}


public function previewCustomerListing($id)
{
    $listing = $this->mobileListingRepo->findWithRelations($id);

    $images = json_decode($listing->image, true) ?? [];

    return [
        'id'        => $listing->id,
        'brand'     => $listing->brand ? $listing->brand->name : null,
        'model'     => $listing->model ? $listing->model->name : null,
        'storage'   => $listing->storage,
        'ram'       => $listing->ram,
        'price'     => $listing->price,
        'condition' => $listing->condition,
        'about'     => $listing->about,
        'customer_id' => $listing->customer_id,
        'image'     => array_map(fn($path) => asset($path), $images),
    ];
}

public function getcustomermobileListing()
{
    $customer = Auth::id();
        $listings = MobileListing::with(['model','customer'])
            ->where('customer_id', $customer)
            ->get()
            ->map(function ($listing) {
                return [
                    'model' => $listing->model ? $listing->model->name : null,
                    'customer' => $listing->customer ? $listing->customer->name : null,
                    'price' => $listing->price,
                    'image' => $listing->image 
                        ? asset(is_array(json_decode($listing->image, true)) 
                            ? json_decode($listing->image, true)[0]  // return only first image
                            : json_decode($listing->image, true)) 
                        : null,
                    'status' => $listing->status,
                ];
            });
        $data = $listings->count() === 1 ? $listings->first() : $listings;
        return $data;
}

public function getcustomernearbyListings($vendorLat, $vendorLng, $radius = 30)
{
    return MobileListing::with(['model','customer'])
        ->select('*', DB::raw("
            (6371 * acos(
                cos(radians($vendorLat)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians($vendorLng)) +
                sin(radians($vendorLat)) * sin(radians(latitude))
            )) AS distance
        "))
        ->having('distance', '<=', $radius) // filter within radius (default 30 km)
        ->orderBy('distance', 'asc')
        ->get()
        ->map(function ($listing) {
            $images = json_decode($listing->image, true) ?? [];

            // Convert all image paths to full URLs
            $imageUrls = array_map(function ($img) {
                return url('admin/assets/images/users/' . basename($img));
            }, $images);

            return [
                'id' => $listing->id,
                'model' => $listing->model ? $listing->model->name : null,
                'customer' => $listing->customer ? $listing->customer->name : null,
                'price' => $listing->price,
                'image' => $imageUrls,
                'distance' => round($listing->distance, 1) . ' km',
            ];
        });
}


   public function getCustomerDeviceDetail($id)
    {
        return MobileListing::with([
            'brand:id,name',
            'model:id,name'
        ])
        ->where('id', $id)
        ->select('id','brand_id','model_id','storage','price','condition','ram','image')
        ->first()
        ->makeHidden(['brand_id', 'model_id']) // hide ids
        ->append(['brand_name','model_name']); // custom attributes
    }

}
