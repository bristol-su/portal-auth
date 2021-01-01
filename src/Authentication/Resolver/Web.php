<?php

namespace BristolSU\Auth\Authentication\Resolver;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

/**
 * Resolve users using the Laravel authentication
 */
class Web implements AuthenticationUserResolver
{

    /**
     * Authentication factory for resolving and setting users
     *
     * @var AuthFactory
     */
    private $auth;

    /**
     * @param AuthFactory $auth Auth factory for resolving and setting users with Laravel
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get the currently logged in user
     *
     * @return AuthenticationUser|null Null if no user found
     */
    public function getUser(): ?AuthenticationUser
    {
        if ($this->hasUser()) {
            return $this->auth->guard('web')->user();
        }
        return null;
    }

    /**
     * Set the logged in user
     *
     * @param AuthenticationUser $user User to set
     */
    public function setUser(AuthenticationUser $user)
    {
        $this->auth->guard('web')->setUser($user);
    }

    /**
     * Log out of the current user
     *
     * @return void
     */
    public function logout(): void
    {
        $this->auth->guard('web')->logout();
    }

    public function hasUser(): bool
    {
        return $this->auth->guard('web')->check();
    }
}
