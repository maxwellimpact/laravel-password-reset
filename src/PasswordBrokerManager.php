<?php

namespace Maxwellimpact\PasswordReset;

use Closure;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\Str;

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
        $key = $this->getAppKey();

        $repository = $config['repository'] ?? null;

        if (isset($this->repositoryCreators[$repository])) {
            return $this->callRepositoryCreator($repository, $config, $key);
        }

        return $this->makeDefaultRepository($config, $key);
    }

    /**
     * Creates the chosen repository using the provided creator.
     *
     * @param string $repository
     * @param array $config
     * @param string $key
     * @return TokenRepositoryInterface
     */
    private function callRepositoryCreator(string $repository, array $config, $key): TokenRepositoryInterface
    {
        return $this->repositoryCreators[$repository]($this->app, $config, $key);
    }

    /**
     * Fallback to the default if no repository was specified.
     *
     * @param array $config
     * @param $key
     * @return DatabaseTokenRepository
     */
    protected function makeDefaultRepository(array $config, $key): DatabaseTokenRepository
    {
        return new DatabaseTokenRepository(
            $this->app['db']->connection($config['connection'] ?? null),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }

    /**
     * @return bool|string
     */
    protected function getAppKey()
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return $key;
    }
}