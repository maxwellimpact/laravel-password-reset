<?php

namespace Maxwellimpact\PasswordReset\Test;

use Illuminate\Support\Facades\Password;
use Maxwellimpact\PasswordReset\InMemoryTokenRepository;
use Maxwellimpact\PasswordReset\PasswordResetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp()
    {
        parent::setUp();

        config(['app.key' => 'blahappkeyblah']);

        Password::repository('in_memory', function($app, $config, $key) {
            return new InMemoryTokenRepository(10);
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            PasswordResetServiceProvider::class,
        ];
    }
}