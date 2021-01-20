<?php

namespace BristolSU\Auth\Social;

use BristolSU\Auth\Social\Contracts\SocialUserRepository as SocialUserRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Handles retrieving and setting authentication users.
 */
class SocialUserRepository implements SocialUserRepositoryContract
{

    /**
     * Get the social user with the given ID
     * @param int $id The ID of the social user
     * @return SocialUser The social user model
     * @throws ModelNotFoundException If the ID is not found
     */
    public function getById(int $id): SocialUser
    {
        return SocialUser::findOrFail($id);
    }

    public function getByProviderId(string $provider, string $providerId): SocialUser
    {
        return SocialUser::where([
            'provider_id' => $providerId,
            'provider' => $provider
        ])->firstOrFail();
    }

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
    public function create(int $authenticationUserId, string $provider, string $providerId, string $email, string $name): SocialUser
    {
        return SocialUser::create([
            'authentication_user_id' => $authenticationUserId,
            'provider' => $provider,
            'provider_id' => $providerId,
            'email' => $email,
            'name' => $name
        ]);
    }
}
