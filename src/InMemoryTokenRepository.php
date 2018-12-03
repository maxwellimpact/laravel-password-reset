<?php

namespace Maxwellimpact\PasswordReset;

use Carbon\Carbon;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InMemoryTokenRepository implements TokenRepositoryInterface
{
    /**
     * The in memory collection of tokens.
     *
     * @var Collection
     */
    protected $tokens;

    /**
     * Seconds until expiry.
     *
     * @var float|int
     */
    private $expires;

    public function __construct($expires = 10)
    {
        $this->expires = $expires;
        $this->tokens = collect([]);
    }

    public function create(CanResetPasswordContract $user)
    {
        $token = Str::random(60);

        $this->tokens->offsetSet($user->getEmailForPasswordReset(), [
            'token' => $token,
            'date_added' => Carbon::now()
        ]);

        return $token;
    }

    public function exists(CanResetPasswordContract $user, $token)
    {
        $match = $this->tokens->get($user->getEmailForPasswordReset(), []);
        return isset($match['token']) && $match['token'] === $token;
    }

    public function delete(CanResetPasswordContract $user)
    {
        return $this->tokens->offsetUnset($user->getEmailForPasswordReset());
    }

    public function deleteExpired()
    {
        $notExpired = $this->tokens->filter(function($token) {
            return !$token['date_added']->addSeconds($this->expires)->isPast();
        });

        $this->tokens = $notExpired;
    }
}