<?php

namespace BristolSU\Auth\Work;

use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GetAuthenticationUserUnit
{

    public function __construct(
        protected RegisterAuthenticationUserUnit $registerUnit,
        protected AuthenticationUserRepository $userRepository
    ) {}

    public function do(User $controlUser): AuthenticationUser
    {
        try {
            $authenticationUser = $this->userRepository->getFromControlId($controlUser->id());
            throw ValidationException::withMessages([
                'identifier' => AlreadyRegisteredMessage::getValue()
            ]);
        } catch (ModelNotFoundException $e) {
            // Authentication user does not exist
        }

        return $this->registerUnit->do($controlUser);
    }

}
