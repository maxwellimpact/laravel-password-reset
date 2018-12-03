<?php

namespace Maxwellimpact\PasswordReset;

use Closure;
use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

class PasswordBrokerManager extends BasePasswordBrokerManager
{
    /**
     * The custom creators for a Token Repository.
     *
     * @var array
     */
    private $repositoryCreators = [];

    /**
     * Register a custom token repository creator Closure.
     *
     * @param  string  $name
     * @param  Closure  $callback
     * @return $this
     */
    public function repository($name, Closure $callback)
    {
        $this->repositoryCreators[$name] = $callback;

        return $this;
    }

    /**
     * The overridden repository creator to allow for custom
     * repository creations.
     *
     * @param array $config
     * @return TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config): TokenRepositoryInterface
    {
        $repository = $config['repository'] ?? null;

        if (isset($this->repositoryCreators[$repository])) {
            return $this->callRepositoryCreator($repository, $config);
        }

        return parent::createTokenRepository($config);
    }

    /**
     * Creates the chosen repository using the provided creator.
     *
     * @param string $repository
     * @param array $config
     * @return TokenRepositoryInterface
     */
    private function callRepositoryCreator(string $repository, array $config): TokenRepositoryInterface
    {
        return $this->repositoryCreators[$repository]($this->app, $config);
    }
}