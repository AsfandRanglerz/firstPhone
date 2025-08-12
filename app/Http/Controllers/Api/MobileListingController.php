<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        return response()->json([
            'message' => 'Listing added successfully',
            'listing' => $listing
        ], 200);

    }  catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while creating the listing',
            'error' => $e->getMessage()
        ], 500);
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
        return response()->json([
            'message' => 'Mobile listings retrieved successfully',
            'data' => $listings
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while retrieving listings',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
