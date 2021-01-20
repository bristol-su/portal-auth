<?php

namespace BristolSU\Auth\Work;

use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Models\User;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;

class RegisterControlUserUnit
{

    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function do(DataUser $dataUser): User
    {
        return $this->userRepository->create($dataUser->id());
    }
}
