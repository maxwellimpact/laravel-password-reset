# Laravel Password Reset
This package extends the default password reset for Laravel to enable custom Token repositories. It also adds an in memory Token Repository for testing.

## Setup

### Install
`composer require maxwellimpact/laravel-password-reset`

### Add the Service Provider
In `config/app.php` replace this `Illuminate\Auth\Passwords\PasswordResetServiceProvider` with `Maxwellimpact\PasswordReset\PasswordResetServiceProvider`

*Note:* If you are using the Laravel 5.4 and up and have Package Discovery on, then just remove the original Laravel provider and it should work fine.  

### Register Your Custom Repository
Register your custom repository creator in one of your Service Providers boot method.
```php
public function boot()
{
    Password::repository('in_memory', function($app, $config, $key) {
        return new InMemoryTokenRepository(10);
    });
}
```

### Configure the Repository
Add your settings in `auth.php`. The `repository` option is required for custom token repositories to be created, otherwise it defaults to the `DatabaseTokenRepository` that Laravel is hardcoded to. All config options will be passed in to your registered creator.
```php
    ...
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'repository' => 'in_memory',
            'expire' => 10,
        ],
    ]
```
 
