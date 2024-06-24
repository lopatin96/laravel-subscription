<?php

namespace Atin\LaravelSubscription\Console;

use App\Models\User;
use App\Notifications\IncompleteSubscription;
use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Models\Order;
use Atin\LaravelSubscription\Models\Subscription;
use Illuminate\Support\Facades\Notification;

class IncompleteSubscriptions
{
    public function __invoke(): void
    {
        foreach (Subscription::with('user')->where('stripe_status', 'incomplete')->get() as $subscription) {
            Notification::send(User::find(1), new IncompleteSubscription($subscription));
        }
    }
}
