<?php

namespace Maxwellimpact\PasswordReset;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider as BasePasswordResetServiceProvider;
use Illuminate\Support\Facades\Password;

class PasswordResetServiceProvider extends BasePasswordResetServiceProvider
{
    /**
     * Register the default password broker instances
     * and override the manager.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        parent::registerPasswordBroker();

        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });
    }

    /**
     * Register the in memory token repository creator.
     *
     * @return void
     */
    public function boot()
    {
        Password::repository('in_memory', function($app, $config) {
            return new InMemoryTokenRepository($config['expire']);
        });
    }
}