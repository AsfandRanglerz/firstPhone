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
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $vendor = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);

        // Free trial check
        if ($plan->price == 0) {
            $alreadyTried = VendorSubscription::where('vendor_id', $vendor->id)
                ->whereHas('plan', function ($q) {
                    $q->where('price', 0);
                })
                ->exists();

            if ($alreadyTried) {
                return response()->json(['message' => 'You already used free trial.'], 403);
            }
        }

        // Dates
        $start = Carbon::now();
        $end = $start->copy()->addDays($plan->duration_days);

        // Save
        $subscription = VendorSubscription::create([
            'vendor_id' => $vendor->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $start,
            'end_date' => $end,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Subscription activated successfully',
            'data' => $subscription
        ], 201);
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
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        return response()->json([
            'subscription' => $subscription,
            'remaining_days' => now()->diffInDays($subscription->end_date, false),
        ]);
    }
}
