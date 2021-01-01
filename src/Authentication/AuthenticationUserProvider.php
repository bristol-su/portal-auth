<?php

namespace BristolSU\Auth\Authentication;

use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\DataUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthenticationUserProvider implements UserProvider
{

    /**
     * @var AuthenticationUserRepository
     */
    private $userRepository;

    public function __construct(AuthenticationUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return AuthenticationUser|null
     */
    public function retrieveById($identifier)
    {
        try {
            return $this->userRepository->getById($identifier);
        } catch (ModelNotFoundException $e) {}
        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        try {
            $user = $this->userRepository->getFromRememberToken($token);
            if($user->id === $identifier) {
                return $user;
            }
        } catch (ModelNotFoundException $e) {}
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable|AuthenticationUser $user
     * @param string $token
     * @return AuthenticationUser|Authenticatable|null
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $this->userRepository->setRememberToken($user->id, $token);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (!array_key_exists('identifier', $credentials)) {
            return null;
        }

        try {
            $dataUser = app(DataUser::class)->getWhere([
                IdentifierAttribute::getValue() => $credentials['identifier']
            ]);
            $controlUser = app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->getByDataProviderId($dataUser->id());
            return $this->userRepository->getFromControlId($controlUser->id());

        } catch (ModelNotFoundException $e) {
        }
        return null;

    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|AuthenticationUser $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if(app(Hasher::class)->check($credentials['password'], $user->password)) {
            return true;
        }
        return false;
    }
}
