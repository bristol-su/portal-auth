<?php

namespace BristolSU\Auth\Work;

use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;

class RegisterDataUserUnit
{

    public function __construct(protected DataUserRepository $dataUserRepository)
    {
    }

    public function do(array $parameters = []): DataUser
    {
        $parameters = array_merge([
            'first_name' => null, 'last_name' => null, 'email' => null, 'dob' => null, 'preferred_name' => null
        ], $parameters);
        return $this->dataUserRepository->create(
            $parameters['first_name'],
            $parameters['last_name'],
            $parameters['email'],
            $parameters['dob'],
            $parameters['preferred_name'],
        );
    }
}
