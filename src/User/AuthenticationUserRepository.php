<?php

namespace BristolSU\Auth\User;

use BristolSU\Auth\User\AuthenticationUser as User;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository as AuthenticationUserRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Handles retrieving and setting authentication users.
 */
class AuthenticationUserRepository implements AuthenticationUserRepositoryContract
{

    /**
     * Get all users registered in the database
     *
     * @return User[]|Collection
     */
    public function all()
    {
        return User::all();
    }

    /**
     * Get a user matching the given control ID
     *
     * @param int $controlId Control ID of the user
     * @return User
     * @throws ModelNotFoundException
     */
    public function getFromControlId(int $controlId): User
    {
        return User::where('control_id', $controlId)->firstOrFail();
    }

    /**
     * Create a user.
     *
     * Attributes should be those in the database
     * [
     *      'control_id' => 1, // ID of the control user model representing the user
     * ];
     *
     * @param array $attributes Attributes to create the user with
     * @return User
     */
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * Get a user by remember token
     *
     * @param string $token Remember token
     * @return User
     * @throws ModelNotFoundException
     */
    public function getFromRememberToken(string $token): User
    {
        return User::where('remember_token', $token)->firstOrFail();
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Set the remember token of a user
     *
     * @param int $id ID of the user
     * @param mixed $token New token for the user
     */
    public function setRememberToken(int $id, $token): void
    {
        $user = $this->getById($id);
        $user->setRememberToken($token);
        $user->save();
    }


}
