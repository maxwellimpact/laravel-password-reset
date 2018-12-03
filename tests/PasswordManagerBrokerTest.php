<?php

namespace Maxwellimpact\PasswordReset\Test;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Support\Facades\Password;
use Maxwellimpact\PasswordReset\InMemoryTokenRepository;

class PasswordManagerBrokerTest extends TestCase
{
    public function testDefaultRepositoryLoads()
    {
        config(['auth.passwords.users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ]]);

        $repository = Password::broker()->getRepository();
        self::assertInstanceOf(DatabaseTokenRepository::class, $repository);
    }

    public function testCorrectRepositoryLoads()
    {
        config(['auth.passwords.users' => [
            'provider' => 'users',
            'repository' => 'in_memory',
            'expire' => 10,
        ]]);

        $repository = Password::broker()->getRepository();
        self::assertInstanceOf(InMemoryTokenRepository::class, $repository);
    }
}