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
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-subscription');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-subscription');

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-subscription');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-subscription')
        ], 'laravel-subscription-views');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/laravel-subscription'),
        ], 'laravel-subscription-lang');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('/migrations')
        ], 'laravel-subscription-migrations');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-subscription.php')
        ], 'laravel-subscription-config');
    }
}