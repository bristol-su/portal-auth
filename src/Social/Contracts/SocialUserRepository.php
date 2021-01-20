<?php

namespace BristolSU\Auth\Social\Contracts;

use BristolSU\Auth\Social\SocialUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Get a social user by the provider and provider id
     *
     * @param string $provider The name of the provider (driver)
     * @param string $providerId The ID of the provider
     * @return SocialUser
     * @throws ModelNotFoundException
     */
    public function getByProviderId(string $provider, string $providerId): SocialUser;

    /**
     * Create a social user
     *
     * @param int $authenticationUserId The ID of the authentication user owning this social user
     * @param string $provider The name of the social provider
     * @param string $providerId The ID of the social user
     * @param string $email The email of the social user
     * @param string $name The name of the social user
     * @return SocialUser
     */
    public function create(int $authenticationUserId, string $provider, string $providerId, string $email, string $name): SocialUser;
}
