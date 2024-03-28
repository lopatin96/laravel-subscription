<?php

namespace Atin\LaravelSubscription\Traits;

use Atin\LaravelSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasSubscription
{
    public function stripeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function stripeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->latest('id');
    }

    public function getSubscribedPlan(): ?array
    {
        return $this->getSubscribedPlanData();
    }

    public function getSubscribedPlanLevel(): ?int
    {
        return $this->getSubscribedPlanData()['level'] ?? null;
    }

    public function getSubscribedPlanName(): ?string
    {
        return $this->getSubscribedPlanData()['name'] ?? null;
    }

    public function getSubscribedPlanPriceType(): ?string
    {
        if (
            ($planData = $this->getSubscribedPlanData())
            && ($stripeSubscription = $this->stripeSubscription)
        ) {
            if ($planData['monthly_id'] === $stripeSubscription->stripe_price) {
                return 'monthly';
            }

            if ($planData['yearly_id'] === $stripeSubscription->stripe_price) {
                return 'yearly';
            }
        }

        return null;
    }

    private function getSubscribedPlanData(): ?array
    {
        if ($stripeSubscription = $this->stripeSubscription) {
            foreach (config('spark.billables.user.plans') as $plan) {
                if (in_array($stripeSubscription->stripe_price, [$plan['monthly_id'], $plan['yearly_id']], true)) {
                    return $plan;
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
        return $this->getSubscribedPlanLevel() === config('spark.billables.user.levels') - 1;
    }
}