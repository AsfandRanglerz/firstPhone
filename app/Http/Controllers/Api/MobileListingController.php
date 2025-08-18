<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MobileListingController extends Controller
{
    public function mobileListing(Request $request)
    {
        try {

            $vendor = Auth::id();
            // Create new listing
            $listing = new MobileListing();
            $listing->brand_id = $request->brand_id;
            $listing->model_id = $request->model_id;
            $listing->storage = $request->storage;
            $listing->ram = $request->ram;
            $listing->color = $request->color;
            $listing->repairing_service = $request->repairing_service;
            $listing->price = $request->price;
            $listing->condition = $request->condition;
            $listing->about = $request->about;
            $listing->vendor_id = auth()->id();

            $mediaPaths = [];

            // Handle multiple images/videos
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move(public_path('admin/assets/images/users/'), $filename);
                    $mediaPaths[] = 'public/admin/assets/images/users/' . $filename;
                }
            }

            // Store as JSON in the image column
            $listing->image = json_encode($mediaPaths);
            $listing->save();

            $Data = [
                'id'      => $listing->id,
                'brand_id' => $listing->brand_id,
                'model_id' => $listing->model_id,
                'storage' => $listing->storage,
                'ram'     => $listing->ram,
                'price'   => $listing->price,
                'condition' => $listing->condition,
                'about'   => $listing->about,
                'vendor_id' => $listing->vendor_id,
                'image'   => array_map(function ($path) {
                    return asset($path);
                }, $mediaPaths),
            ];

            return ResponseHelper::success($Data, 'Listing added successfully', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while creating the listing', 'error', 500);
        }
    }

    

public function previewListing($id)
{
    try {
        // Fetch listing with relations
        $listing = MobileListing::with(['brand', 'model'])
            ->where('id', $id)
            ->firstOrFail();

        // Decode images (JSON stored in DB)
        $images = json_decode($listing->image, true) ?? [];

        $Data = [
            'id'        => $listing->id,
            'brand'     => $listing->brand ? $listing->brand->name : null,
            'model'     => $listing->model ? $listing->model->name : null,
            'storage'   => $listing->storage,
            'ram'       => $listing->ram,
            'price'     => $listing->price,
            'condition' => $listing->condition,
            'about'     => $listing->about,
            'vendor_id' => $listing->vendor_id,
            'image'     => array_map(function ($path) {
                return asset($path);
            }, $images),
        ];

        return ResponseHelper::success($Data, 'Preview generated successfully', null, 200);

    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while generating preview', 'error', 500);
    }
}


public function getmobileListing()
{
    try{
        $vendor = Auth::id();
        $listings = MobileListing::with('model')
            ->where('vendor_id', $vendor)
            ->get()
            ->map(function ($listing) {
                return [
                    'model' => $listing->model ? $listing->model->name : null,
                    'price' => $listing->price,
                    'image' => $listing->image ? array_map(function ($path) {
                        return asset($path);
                    }, json_decode($listing->image, true) ?? []) : [],
                    'status' => $listing->status,
                ];
            });
        $data = $listings->count() === 1 ? $listings->first() : $listings;
        return ResponseHelper::success($listings, 'Mobile listings retrieved successfully', null, 200);

    } catch (ValidationException $e) {
        return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving the listing', 'error', 500);
    }


}
}
