# Install

### Migration
Run migration
```php
php artisan migrate
```

### Trait
Add  **fillable**, **casts** and **HasSubscription** trait to User model

```php
use Atin\LaravelSubscription\Traits\HasSubscription;

class User extends Authenticatable
{
    use HasSubscription, …

    protected $fillable = [
        …
        'billing_visited_at',
    ];
    
    protected $casts = [
        …
        'billing_visited_at' => 'datetime',
    ];
```

### Nova
Fields
```php
Boolean::make('Billing', 'billing_visited_at')
    ->onlyOnIndex()
    ->sortable(),

DateTime::make('Billing Visited At', 'billing_visited_at')
    ->hideFromIndex()
    ->nullable()
    ->readonly(),
```

### Register
For example, in ```spark/app.blade.php```:
```php
@php
    auth()->user()->update([
        'billing_visited_at' => now(),
    ]);

    activity()
        ->causedBy(auth()->user())
        ->log('billing:visited');
@endphp
```

### Views
Add subscription info component to *resources/views/layouts/app.blade.php*:
```html
<body class="font-sans antialiased">
    @include('laravel-subscription::subscription.info')
    …
```

### Console
Add ```IncompleteSubscriptions``` to ```app/Console/Kernel.php```
```php
use Atin\LaravelSubscription\Console\IncompleteSubscriptions;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(new IncompleteSubscriptions)->hourly()->between('6:00', '24:00');
```

### Config
Publish config to manage limited version of config:
```php
php artisan vendor:publish --tag="laravel-subscription-config"
```

# Nova
### Resource
```php
<?php

namespace App\Nova;

use Atin\LaravelUserStatuses\Enums\UserStatus;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Http\Requests\NovaRequest;
use Marshmallow\Filters\DateRangeFilter;
use Atin\LaravelSubscription\Models\Subscription as SubscriptionModel;

class Subscription extends Resource
{
    public static string $model = SubscriptionModel::class;

    public static $title = 'stripe_id';

    public static $search = [
        'id', 'stripe_id', 'user.name', 'user.email',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            Stack::make('User', [
                BelongsTo::make('User')
                    ->peekable()
                    ->nullable()
                    ->readonly(),

                Line::make(null, function () {
                    return $this->user?->email ?: 'No email'
                        . ($this->user->status === UserStatus::Blocked ? ' (blocked)' : null);
                })
                    ->asSmall(),

                Line::make(null, function () {
                    return 'Stripe: '.($this->user?->stripe_id ?: '—');
                })
                    ->asSmall(),

                Line::make(null, function () {
                    return "User Created: {$this->user?->created_at->diffForHumans()}";
                })
                    ->asSmall(),

                Line::make(null, function () {
                    return "Last Online: {$this->user?->last_seen_at?->diffForHumans()}";
                })
                    ->asSmall(),
            ])
                ->sortable(),

            Status::make('Status', 'stripe_status')
                ->loadingWhen(['incomplete'])
                ->failedWhen(['canceled', 'past_due'])
                ->sortable(),

//            Number::make(__('Links'), function () {
//                return $this->user->links->count();
//            }),
//
//            Number::make(__('Clicks'), function () {
//                return $this->user->clicks->count();
//            }),

            Stack::make('Trial ends At', [
                DateTime::make('Trial ends At'),

                Line::make(null, function () {
                    return $this->trial_ends_at ? "({$this->trial_ends_at->diffForHumans()})" : null;
                })
                    ->asSmall(),
            ])
                ->sortable()
                ->readonly(),

            Stack::make('Ends At', [
                DateTime::make('Ends At'),

                Line::make(null, function () {
                    return $this->ends_at ? "({$this->ends_at->diffForHumans()})" : null;
                })
                    ->asSmall(),
            ])
                ->sortable()
                ->readonly(),
                
            Stack::make('Created At', [
                DateTime::make('Created At'),

                Line::make(null, function () {
                    return "({$this->created_at->diffForHumans()})";
                })
                    ->asSmall(),
            ])
                ->sortable()
                ->readonly(),

            Stack::make('Updated At', [
                DateTime::make('Created At'),

                Line::make(null, function () {
                    return "({$this->updated_at->diffForHumans()})";
                })
                    ->asSmall(),
            ])
                ->sortable()
                ->readonly(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            new Metrics\ActiveSubscriptions,
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new DateRangeFilter('created_at', 'Created Date'),
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }
}
```
### Metric
```php
<?php

namespace App\Nova\Metrics;

use Atin\LaravelSubscription\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class ActiveSubscriptions extends Partition
{
    public $width = '1/4';

    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Subscription::where('stripe_status', 'active'), 'stripe_price')
            ->label(fn ($value) => match ($value) {
                'price_1MBekyFc1SsSrqC8CC7epdr9', 'price_1MBmAbFc1SsSrqC8T4zjWYq4' => 'Premium (monthly)',
                'price_1MBekyFc1SsSrqC8AyZ37iye', 'price_1MBmAbFc1SsSrqC8h2aElxW2' => 'Premium (yearly)',
                'price_1MzI8DFc1SsSrqC8AB0fCkPs', 'price_1MzI9MFc1SsSrqC8Mcyt219g' => 'Premium+ (monthly)',
                'price_1MzI8DFc1SsSrqC8JqoNO3nx', 'price_1MzI9MFc1SsSrqC8NPKE914s' => 'Premium+ (yearly)',
                default => ucfirst($value)
            })->colors([
                'Premium (monthly)' => '#eef2ff',
                'Premium (yearly)' => '#c7d2fe',
                'Premium+ (monthly)' => '#818cf8',
                'Premium+ (yearly)' => '#4f46e5',
            ]);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'active-subscriptions';
    }
}
```

# Publishing
### Localization
```php
php artisan vendor:publish --tag="laravel-subscription-lang"
```

### Views
```php
php artisan vendor:publish --tag="laravel-subscription-views"
```

### Config
```php
php artisan vendor:publish --tag="laravel-subscription-config"
```

### Migrations
```php
php artisan vendor:publish --tag="laravel-subscription-migrations"
```