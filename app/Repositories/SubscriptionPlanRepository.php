<?php

namespace App\Repositories;

use App\Models\SubscriptionPlan;
use App\Repositories\Interfaces\SubscriptionPlanInterface;

class SubscriptionPlanRepository implements SubscriptionPlanInterface
{
    public function all()
    {
        return SubscriptionPlan::all();
    }

    public function find($id)
    {
        return SubscriptionPlan::findOrFail($id);
    }

    public function create(array $data)
    {
        return SubscriptionPlan::create($data);
    }

    public function update($id, array $data)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update($data);
        return $plan;
    }

    public function delete($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        return $plan->delete();
    }
}
