<?php

namespace App\Repositories\Api;

use App\Models\SubscriptionPlan;
use App\Models\VendorSubscription;
use App\Repositories\Api\Interfaces\VendorSubscriptionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VendorSubscriptionRepository implements VendorSubscriptionRepositoryInterface
{

    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'subscription_plan_id' => 'required|exists:subscription_plans,id',
            ]);

            $vendor = auth()->user();
            $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);

            // 1. Check if vendor has already used a free plan (ever)
            $hasUsedFreePlan = VendorSubscription::where('vendor_id', $vendor->id)
                ->whereHas('plan', function ($q) {
                    $q->where('price', 0);
                })
                ->exists();

            // 2. Check for any active paid subscription
            $hasActivePaidPlan = VendorSubscription::where('vendor_id', $vendor->id)
                ->where('is_active', true)
                ->where('end_date', '>=', now())
                ->whereHas('plan', function ($q) {
                    $q->where('price', '>', 0);
                })
                ->exists();

            // 3. Validation Logic

            // Case A: User tries to subscribe to free plan but already used it
            if ($plan->price == 0 && $hasUsedFreePlan) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => 'You have already used your free trial once.',
                ], 403);
            }

            // Case B: User already has an active paid plan
            if ($hasActivePaidPlan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You already have an active paid subscription.',
                ], 400);
            }

            // Calculate new subscription duration
            $start = now();
            $end = $start->copy()->addDays($plan->duration_days);

            // If vendor upgrades (e.g. from free to paid), deactivate old free plan
            VendorSubscription::where('vendor_id', $vendor->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Create new subscription
            $subscription = VendorSubscription::create([
                'vendor_id' => $vendor->id,
                'subscription_plan_id' => $plan->id,
                'start_date' => $start,
                'end_date' => $end,
                'is_active' => true,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription activated successfully.',
                'data' => $subscription
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'server_error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }


    public function current(Request $request)
    {
        $vendor = auth()->user();

        $subscription = VendorSubscription::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['status' => 'not_found','message' => 'No active subscription found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'subscription' => $subscription,
            'remaining_days' => now()->diffInDays($subscription->end_date, false),
        ]);
    }
}
