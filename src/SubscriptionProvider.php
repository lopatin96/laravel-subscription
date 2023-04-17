<?php

namespace Atin\LaravelSubscription;

use Illuminate\Support\ServiceProvider;

class SubscriptionProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-subscription');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-subscription.php')
        ], 'laravel-subscription');
    }
}