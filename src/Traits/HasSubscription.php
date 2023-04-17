<?php

namespace Atin\LaravelSubscription\Traits;

use Illuminate\Support\Facades\DB;

trait HasSubscription
{
    public function getSubscribedPlan(): int|null
    {
        if ($this->subscribed()) {
            foreach (config('spark.billables.user.plans') as $idx => $plan) {
                if (
                    DB::table('subscriptions')->where('user_id', $this->id)
                        ->where('stripe_status', 'active')
                        ->whereIn('stripe_price', [
                            $plan['monthly_id'],
                            $plan['yearly_id'],
                        ])
                        ->first()
                ) {
                    return $idx;
                }
            }
        }

        return null;
    }
}