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
        $listing->price = $request->price;
        $listing->condition = $request->condition;
        $listing->about = $request->about;
        $listing->vendor_id = auth()->id(); 

        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('admin/assets/images/users/'), $filename);
            $listing->image = 'public/admin/assets/images/users/' . $filename; 
        }

        $listing->save();

        return ResponseHelper::success($listing, 'Listing added successfully', null, 200);

    } catch (\Exception $e) {
        return ResponseHelper::error($e->getMessage(), 'An error occurred while creating the listing', 'error', 500);
    }
}

public function getmobileListing()
{
    try{
        $vendor = Auth::id();
        $listings = MobileListing::where('vendor_id', $vendor)
            ->get()
            ->map(function ($listing) {
                return [
                    'model' => $listing->model_id,
                    'price' => $listing->price,
                    'image' => $listing->image,
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
