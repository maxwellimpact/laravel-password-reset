<?php

namespace Maxwellimpact\PasswordReset;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider as BasePasswordResetServiceProvider;

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
}