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
            'brand' => $request->brand,
            'model' => $request->model,
            'storage' => $request->storage,
            'price' => $request->price,
            'condition' => $request->condition,
            'color' => $request->color,
            'ram' => $request->ram,
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
            'pta_approved' => $request->pta_approved,
            'quantity' => $request->quantity,
            'location' => $request->location,
            'images' => $request->images, // text/path save hoga
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

        // Get all carts for this user
        $carts = MobileCart::where('user_id', $user->id)
            ->get([ 'id', 'model', 'price', 'location', 'images']);

        // Calculate subtotal
        $subtotal = $carts->sum('price');

        return response()->json([
            'status' => true,
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
