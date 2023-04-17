# Install
### Trait
Add **HasSubscription** trait to User model

```php

use Atin\LaravelSubscription\Traits\HasSubscription;

class User extends Authenticatable
{
    use HasSubscription, …
```

# Publishing
### Config
```php
php artisan vendor:publish --tag="laravel-subscription-config"
```