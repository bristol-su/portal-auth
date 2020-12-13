<?php

namespace BristolSU\Auth\Tests\helpers;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\User\AuthenticationUser;

class SessionAuthenticationResolver implements AuthenticationUserResolver
{
    private $user;

    public function __construct(AuthenticationUser $user = null)
    {
        $this->user = $user;
    }

    public function getUser(): ?AuthenticationUser
    {
        return $this->user;
    }

    public function setUser(AuthenticationUser $user)
    {
        $this->user = $user;
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    public function logout(): void
    {
        $this->user = null;
    }
}
