<?php

namespace BristolSU\Auth\Work;

use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;

class RegisterDataUserUnit
{

    public function do(array $parameters = []): DataUser
    {
        $functionParameters = array_merge([
            'firstName' => null, 'lastName' => null, 'email' => null, 'dob' => null, 'preferredName' => null
        ], $parameters);
        return app()->call(DataUserRepository::class . '@create', $functionParameters);
    }
}
