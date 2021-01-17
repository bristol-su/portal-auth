<?php

namespace BristolSU\Auth\Social\Contracts;

use BristolSU\Auth\Social\SocialUser;

/**
 * Handle retrieving and creating users from social login
 */
interface SocialUserRepository
{

    /**
     * Get a social user by its ID
     *
     * @param int $id The ID of the social user
     * @return SocialUser
     */
    public function getById(int $id): SocialUser;

}
