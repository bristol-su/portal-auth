<?php

namespace BristolSU\Auth\Work;

use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage;
use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use Illuminate\Validation\ValidationException;

class GetControlUserUnit
{

    public function __construct(
        protected RegisterControlUserUnit $registerUnit,
        protected UserRepository $userRepository
    ) {}

    public function do(DataUser $dataUser)
    {
        try {
            return $this->userRepository->getByDataProviderId($dataUser->id());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (ControlUserRegistrationEnabled::getValue()) {
                return $this->registerUnit->do($dataUser);
            }
        }

        throw ValidationException::withMessages([
            'identifier' => ControlUserRegistrationNotAllowedMessage::getValue()
        ]);
    }
}
