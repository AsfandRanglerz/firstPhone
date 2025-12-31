<?php

namespace App\Http\Controllers\Api;

use App\Models\MobileCart;
use App\Models\VendorMobile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MobileCartController extends Controller
{
    //

   public function store(Request $request)
{
    try {

        $userId = Auth::id();
        $listingId = $request->mobile_listing_id;
        $quantity = $request->quantity;

        // ✅ Get mobile listing with vendor + stock
        $mobile = VendorMobile::find($listingId);

        if (!$mobile) {
            return response()->json([
                'message' => 'Mobile listing not found'
            ], 404);
        }

        // 🛑 Stop if requested quantity > stock
        if ($quantity > $mobile->stock) {
            return response()->json([
                'message' => 'Quantity cannot be greater than available stock'
            ], 409);
        }


        // 🔍 Check all ACTIVE cart items of user
        $existingCarts = MobileCart::where('user_id', $userId)
            ->where('is_ordered', 0)
            ->get();


        // If user already has items in cart
        if ($existingCarts->count() > 0) {

            // Get the vendor of first cart item
            $currentVendorId = $existingCarts->first()->mobileListing->vendor_id;

            // 🛑 Block if trying to add item from DIFFERENT vendor
            if ($currentVendorId != $mobile->vendor_id) {
                return response()->json([
                    'message' => 'Please place order or clear your cart before adding another item from a different vendor'
                ], 409);
            }

            // 🛑 If SAME item already exists in cart
            $sameItem = $existingCarts->where('mobile_listing_id', $listingId)->first();

            if ($sameItem) {
                return response()->json([
                    'message' => 'Item already added to cart. Please check your cart.'
                ], 409);
            }
        }


        // ✅ Safe to add item
        $mobileCart = MobileCart::create([
            'user_id' => $userId,
            'mobile_listing_id' => $listingId,
            'quantity' => $quantity,
            'is_ordered' => 0
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
        ->get(['id', 'mobile_listing_id', 'quantity']);

    // Calculate subtotal (price × quantity)
    $subtotal = $carts->sum(function ($cart) {
        return ($cart->mobileListing->price ?? 0) * ($cart->quantity ?? 1);
    });

    return response()->json([
        'message' => 'Cart details fetched successfully',
        'user_id' => $user->id,
        'data' => $carts,
        'subtotal_price' => $subtotal
    ], 200);
}

public function updateQuantity(Request $request)
{
    try {

        $userId = Auth::id();

        // 🛒 Find the cart item of this user that is not ordered yet
        $cart = MobileCart::where('id', $request->cart_id)
            ->where('user_id', $userId)
            ->where('is_ordered', 0)
            ->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart item not found'
            ], 404);
        }

        // 📦 Fetch stock from vendor_mobiles
        $stock = $cart->mobileListing->stock ?? 0;

        // ❌ If requested quantity > stock
        if ($request->quantity > $stock) {
            return response()->json([
                'message' => 'Quantity cannot be greater than available stock'
            ], 422);
        }

        // ✅ Update quantity
        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json([
            'message' => 'Cart quantity updated successfully',
            'data' => [
                'id' => $cart->id,
                'mobile_listing_id' => $cart->mobile_listing_id,
                'quantity' => $cart->quantity
            ]
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Something went wrong!',
            'error' => $e->getMessage()
        ], 500);
    }
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

    $id = $request->query('id');

    // Find cart item
    $cart = MobileCart::where('id', $id)
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
