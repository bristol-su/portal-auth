<?php

namespace BristolSU\Auth\Work;

use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Models\User;

class RegisterAuthenticationUserUnit
{

    public function __construct(
        protected AuthenticationUserRepository $userRepository
    ) {}

    public function do(User $controlUser): AuthenticationUser
    {
        return $this->userRepository->create(['control_id' => $controlUser->id()]);

    }

}
