<?php

namespace Atin\LaravelSubscription\Traits;

use Atin\LaravelSubscription\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

trait HasSubscription
{
    public function getSubscribedPlan(): int|null
    {
        if ($this->subscribed()) {
            foreach (config('spark.billables.user.plans') as $idx => $plan) {
                if (
                    Subscription::where('user_id', $this->id)
                        ->where('stripe_status', 'active')
                        ->whereIn('stripe_price', [$plan['monthly_id'], $plan['yearly_id']])
                        ->first()
                ) {
                    return $idx;
                }
            }
        }

        return null;
    }

    public function getSubscribedAttribute(): bool
    {
        return Subscription::where('user_id', $this->id)
            ->where('stripe_status', 'active')
            ->exists();
    }

    public function isMaximumSubscribedPlan(): bool
    {
        return $this->getSubscribedPlan() === count(config('spark.billables.user.plans')) - 1;
    }
}