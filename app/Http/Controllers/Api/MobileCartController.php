<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileCartController extends Controller
{
    //

    public function store(Request $request)
{
    try {
        $mobileCart = MobileCart::create([
            'user_id' => Auth::id(), // Authenticated user id
            'mobile_listing_id' => $request->mobile_listing_id, // Mobile listing id from request
        ]);

        return response()->json([
            'message' => 'Mobile added to cart successfully',
            'data' => $mobileCart
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong!',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getCart(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    // Get carts with related mobile listing
    $carts = MobileCart::where('user_id', $user->id)
        ->with(['mobileListing' => function($query) {
            $query->select('id', 'model_id', 'price', 'location', 'image');
        }])
        ->get(['id', 'mobile_listing_id']);

    // Calculate subtotal
    $subtotal = $carts->sum(function ($cart) {
        return $cart->mobileListing->price ?? 0;
    });

    return response()->json([
        'message' => 'Cart details have been fetched successfully.',
        'user_id' => $user->id,
        'data' => $carts,
        'subtotal_price' => $subtotal
    ], 200);
}


    public function deleteCart(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    // Find cart item
    $cart = MobileCart::where('id', $request->id)
        ->where('user_id', $user->id) // ensure item belongs to the logged-in user
        ->first();

    if (!$cart) {
        return response()->json([
            'status' => false,
            'message' => 'Cart item not found',
        ], 404);
    }

    // Delete the record
    $cart->delete();

    return response()->json([
        'message' => 'Cart item deleted successfully',
    ], 200);
}


}
