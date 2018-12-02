<?php

namespace Maxwellimpact\PasswordReset\Test;

use Illuminate\Foundation\Auth\User;
use Maxwellimpact\PasswordReset\InMemoryTokenRepository;

class InMemoryTokenRepositoryTest extends TestCase
{
    /**
     * @var InMemoryTokenRepository
     */
    protected $repository;

    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new InMemoryTokenRepository(1);
        $this->user = $this->makeUser();
    }

    public function testCreate()
    {
        $token = $this->repository->create($this->user);

        self::assertNotNull($token);
        self::assertTrue((bool) preg_match('/^[A-Za-z0-9]{60}$/', $token), "Key doesn't match the pattern");
    }

    public function testDelete()
    {
        $user2 = $this->makeUser();
        $token1 = $this->repository->create($this->user);
        $token2 = $this->repository->create($user2);
        $this->repository->delete($this->user);

        self::assertFalse($this->repository->exists($this->user, $token1));
        self::assertTrue($this->repository->exists($user2, $token2));
    }

    public function testExists()
    {
        $token1 = $this->repository->create($this->user);
        self::assertTrue($this->repository->exists($this->user, $token1));

        $token2 = $this->repository->create($this->user);
        self::assertTrue($this->repository->exists($this->user, $token2));
        self::assertFalse($this->repository->exists($this->user, $token1));
    }

    public function testDeleteExpired()
    {
        $user2 = $this->makeUser();
        $token1 = $this->repository->create($this->user);
        $token2 = $this->repository->create($user2);

        sleep(2);
        $this->repository->deleteExpired();

        self::assertFalse($this->repository->exists($this->user, $token1));
        self::assertFalse($this->repository->exists($user2, $token2));
    }

    /**
     * @return User
     */
    protected function makeUser(): User
    {
        $user = new User();
        $user->email = $this->faker->email;
        return $user;
    }

}