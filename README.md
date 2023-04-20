# Install
### Trait
Add **HasSubscription** trait to User model

```php

use Atin\LaravelSubscription\Traits\HasSubscription;

class User extends Authenticatable
{
    use HasSubscription, …
```

### Views
Add subscription card view to *resource/views/profile/show.blade.php*

```html
@include('laravel-subscription::subscription.card')
```

and subscription info component to *resources/views/layouts/app.blade.php*:
```html
<body class="font-sans antialiased">
    @include('laravel-subscription::subscription.info')
    …
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