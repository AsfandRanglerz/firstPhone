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

            $mobiles = MobileListing::whereRaw('LOWER(model) LIKE ?', ['%' . strtolower($query) . '%'])
                ->where('status', 0)
                ->select('id', 'model', 'price', 'image')
                ->get();


            if ($mobiles->isEmpty()) {
                return response()->json(['message' => 'No matching mobile found'], 404);
            }

            // Add to recent searches if we got results
            $this->addToRecentSearch($query, $user->id);

            return response()->json($mobiles);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while searching',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function addToRecentSearch($query, $userId)
    {
        try {
            Recentsearch::create([
                'model' => $query,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Something went wrong: ' . $e->getMessage());
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
