<?php

namespace BristolSU\Auth\User\Contracts;

use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Handle creating and retrieving users from the database
 */
interface AuthenticationUserRepository
{

    /**
     * Get a user matching the given control ID
     *
     * @param int $controlId Control ID of the user
     * @return AuthenticationUser
     */
    public function getFromControlId(int $controlId): AuthenticationUser;

    /**
     * Create a user.
     *
     * Attributes should be those in the database
     * [
     *      'control_id' => 1, // ID of the control user model representing the user
     * ];
     *
     * @param array $attributes Attributes to create the user with
     * @return AuthenticationUser
     */
    public function create(array $attributes): AuthenticationUser;

    /**
     * Get all users registered in the database
     *
     * @return AuthenticationUser[]|Collection
     */
    public function all();

    /**
     * Get a user by remember token
     *
     * @param string $token Remember token
     * @return AuthenticationUser
     * @throws ModelNotFoundException
     */
    public function getFromRememberToken(string $token): AuthenticationUser;

    /**
     * Get a user by ID
     *
     * @param int $id ID of the user
     * @return AuthenticationUser
     * @throws ModelNotFoundException
     */
    public function getById(int $id): AuthenticationUser;

    /**
     * Set the remember token of a user
     *
     * @param int $id ID of the user
     * @param mixed $token New token for the user
     */
    public function setRememberToken(int $id, $token): void;
}
