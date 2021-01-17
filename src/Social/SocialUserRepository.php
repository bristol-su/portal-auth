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
}
