<?php

namespace BristolSU\Auth\Work;

use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class GetDataUserUnit
{

    public function __construct(
        protected RegisterDataUserUnit $registerUnit,
        protected DataUserRepository $dataUserRepository
    )
    {

    }

    /**
     * @param string $identifier
     * @return \BristolSU\ControlDB\Contracts\Models\DataUser
     * @throws ValidationException
     */
    public function do(string $identifier)
    {
        $parameters = [IdentifierAttribute::getValue() => $identifier];
        try {
            return $this->dataUserRepository->getWhere($parameters);
        } catch (ModelNotFoundException $e) {
            if (DataUserRegistrationEnabled::getValue()) {
                return $this->registerUnit->do($parameters);
            }
        }

        throw ValidationException::withMessages([
            'identifier' => DataUserRegistrationNotAllowedMessage::getValue()
        ]);
    }
}
