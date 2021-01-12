<?php

namespace BristolSU\Auth\Authentication\Contracts;

use BristolSU\Auth\User\AuthenticationUser;

/**
 * Contract to set or resolve a logged in user
 */
interface AuthenticationUserResolver
{

    /**
     * Get the currently set user
     *
     * @return AuthenticationUser|null Returns the user, or null if no user found
     */
    public function getUser(): ?AuthenticationUser;

    /**
     * Set the currently logged in user
     *
     * @param AuthenticationUser $user User to log in
     * @return void
     */
    public function setUser(AuthenticationUser $user);

    /**
     * @return bool
     */
    public function hasUser(): bool;

    /**
     * Log out of the current user
     *
     * @return void
     */
    public function logout(): void;

}
