<?php

namespace Atin\LaravelSubscription\Traits;

use Atin\LaravelSubscription\Models\Subscription;

trait HasSubscription
{
    public function stripeSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function stripeSubscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class)
            ->latest('id');
    }

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