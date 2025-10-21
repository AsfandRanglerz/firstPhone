<?php

namespace App\Services\Api;

use Carbon\Carbon;
use App\Models\VendorMobile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Api\VendorMobileListingRepository;

class VendorMobileListingService
{
    protected $vendormobileListingRepo;

    public function __construct(VendorMobileListingRepository $vendormobileListingRepo)
    {
        $this->vendormobileListingRepo = $vendormobileListingRepo;
    }

     public function createListing($request)
    {
        $vendorId = Auth::id();

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
            'brand_id' => $request->brand_id,
            'model_id' => $request->model_id,
            'storage' => $request->storage,
            'ram' => $request->ram,
            'color' => $request->color,
            'price' => $request->price,
            'condition' => $request->condition,
            'about' => $request->about,
            'processor' => $request->processor,
            'display' => $request->display,
            'charging' => $request->charging,
            'refresh_rate' => $request->refresh_rate,
            'main_camera' => $request->main_camera,
            'ultra_camera' => $request->ultra_camera,
            'telephoto_camera' => $request->telephoto_camera,
            'front_camera' => $request->front_camera,
            'build' => $request->build,
            'wireless' => $request->wireless,
            'stock' => $request->stock,
            'ai_features' => $request->ai_features,
            'battery_health' => $request->battery_health,
            'os_version' => $request->os_version,
            'warranty_start' => $request->warranty_start,
            'warranty_end' => $request->warranty_end,
            'vendor_id' => $vendorId,
            'image' => json_encode($mediaPaths),
        ];

        $listing = $this->vendormobileListingRepo->create($data);

        $data['id'] = $listing->id;
        $data['image'] = array_map(fn($path) => asset($path), $mediaPaths);

        return $data;
    }

    public function previewListing($id)
    {
        $listing = $this->vendormobileListingRepo->findWithRelations($id);

        $images = json_decode($listing->image, true) ?? [];

        return [
            'id'        => $listing->id,
            'brand'     => $listing->brand ? $listing->brand->name : null,
            'model'     => $listing->model ? $listing->model->name : null,
            'storage'   => $listing->storage,
            'color'     => $listing->color,
            'ram'       => $listing->ram,
            'price'     => $listing->price,
            'condition' => $listing->condition,
            'about'     => $listing->about,
            'processor' => $listing->processor,
            'display'   => $listing->display,
            'charging'  => $listing->charging,
            'refresh_rate' => $listing->refresh_rate,
            'main_camera' => $listing->main_camera,
            'ultra_camera' => $listing->ultra_camera,
            'telephoto_camera' => $listing->telephoto_camera,
            'front_camera' => $listing->front_camera,
            'build'     => $listing->build,
            'wireless'  => $listing->wireless,
            'stock'     => $listing->stock,
            'ai_features' => $listing->ai_features,
            'battery_health' => $listing->battery_health,
            'os_version' => $listing->os_version,
            'warranty_start' => $listing->warranty_start,
            'warranty_end' => $listing->warranty_end,
            'quantity' => $listing->quantity,
            'vendor_id' => $listing->vendor_id,
            'image'     => array_map(fn($path) => asset($path), $images),
        ];
    }
}
