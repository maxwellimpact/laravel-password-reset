# Laravel Password Reset
Extends the default password reset for Laravel to enable custom Token repositories. It also adds an in memory Token Repository for testing.

## Setup

### Install
`composer require maxwellimpact/laravel-password-reset`

### Add the Service Provider
In `config/app.php` replace `Illuminate\Auth\Passwords\PasswordResetServiceProvider` with `Maxwellimpact\PasswordReset\PasswordResetServiceProvider`

*Note:* If you are using Laravel 5.5 and up and have Package Discovery on, then just remove the original Laravel provider and it should work fine.  

### Register Your Custom Repository
Register your custom repository creator in one of your Service Providers boot method. The `in_memory` repository is already registered by default.
```php
public function boot()
{
    Password::repository('in_memory', function($app, $config) {
        return new InMemoryTokenRepository($config['expire']);
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
 
