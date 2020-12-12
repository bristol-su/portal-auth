<?php

namespace BristolSU\Auth\Authentication\Resolver;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

/**
 * Resolve a user from the API authentication
 */
class Api implements AuthenticationUserResolver
{
    /**
     *  User authentication factory for resolving and setting users
     *
     * @var AuthFactory
     */
    private $auth;

    /**
     * @param AuthFactory $auth Factory to resolve the user from
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get the user from the API authentication
     *
     * @return AuthenticationUser|null Logged in user, or null if no user found.
     */
    public function getUser(): ?AuthenticationUser
    {
        if ($this->hasUser()) {
            return $this->auth->guard('api')->user();
        }
        return null;
    }

    /**
     * Set a user. This method cannot be used since the user cannot be set for an API authentication
     *
     * @param AuthenticationUser $user User to log in
     * @return void
     * @throws \Exception Always, the user cannot be set.
     */
    public function setUser(AuthenticationUser $user)
    {
        $this->auth->guard('api')->setUser($user);
    }

    /**
     * Log out of the current user
     *
     * @return void
     * @throws \Exception
     */
    public function logout(): void
    {
        $this->auth->guard('api')->logout();
    }

    public function hasUser(): bool
    {
        return $this->auth->guard('api')->check();
    }
}
