<?php

namespace App\Http\Controllers\Api;

use App\Models\Recentsearch;
use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MobileSearchController extends Controller
{
    //
    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $query = trim($request->input('name'));

            if (!$query) {
                return response()->json([
                    'message' => 'Search query required'
                ], 400);
            }

            $words = explode(' ', strtolower($query)); // Split query into words

            // Search vendor_mobiles with model & brand
            $mobiles = \App\Models\VendorMobile::with(['model', 'brand'])
                ->where('stock', '>', 0)
                ->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->where(function ($q2) use ($word) {
                            $q2->whereHas('model', function ($q3) use ($word) {
                                $q3->whereRaw('LOWER(name) LIKE ?', ["%$word%"]);
                            })
                            ->orWhereHas('brand', function ($q3) use ($word) {
                                $q3->whereRaw('LOWER(name) LIKE ?', ["%$word%"]);
                            });
                        });
                    }
                })
                ->select('id', 'model_id', 'brand_id', 'price', 'image')
                ->get();

            if ($mobiles->isEmpty()) {
                return response()->json(['message' => 'No matching mobile found'], 404);
            }

            // Add recent search only if user is authenticated
            if ($user) {
                $this->addToRecentSearch($query, $user->id, $mobiles);
            }

            // Format response
            $response = $mobiles->map(function ($m) {
                $images = json_decode($m->image, true) ?? [];

                return [
                    'id'       => $m->id,
                    'model_id' => $m->model_id,
                    'model'    => $m->model?->name,
                    'brand_id' => $m->brand_id,
                    'brand'    => $m->brand?->name,
                    'price'    => $m->price,
                    'image'    => isset($images[0]) ? asset($images[0]) : null,
                ];
            });

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while searching',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add recent search (with model_id & brand_id)
     */
    private function addToRecentSearch($query, $userId, $mobiles)
    {
        try {
            // Take first matching mobile for storing model_id & brand_id
            $firstMobile = $mobiles->first();

            if ($firstMobile) {
                \App\Models\Recentsearch::create([
                    'user_id'   => $userId,
                    'model'     => $query,
                    'model_id'  => $firstMobile->model_id,
                    'brand_id'  => $firstMobile->brand_id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Something went wrong while adding recent search: ' . $e->getMessage());
        }
    }



    public function getRecentSearches()
    {
        try {
            // Current logged-in user ki ID
            $userId = Auth::id();

            // Agar user login nahi hai
            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Database se user ki recent searches get karna
            $recentSearches = DB::table('recentsearches')
                ->where('user_id', $userId)
                ->select('id', 'model', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $recentSearches
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function delete(Request $request)
    {
        try {
            $id = trim($request->input('id'));

            if (empty($id)) {
                return response()->json([
                    'message' => 'ID is required',
                ], 400);
            }

            $find = Recentsearch::find($id);

            if (!$find) {
                return response()->json([
                    'message' => 'History not found',
                ], 404);
            }

            $find->delete();

            return response()->json([
                'message' => 'Deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteAll(Request $request)
    {
        try {
            // Table truncate karna (poora data remove)
            Recentsearch::truncate();

            return response()->json([
                'message' => 'All history deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
