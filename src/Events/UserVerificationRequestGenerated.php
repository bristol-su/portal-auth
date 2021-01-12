<?php

namespace BristolSU\Auth\Events;

use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserVerificationRequestGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * The authentication user requesting email verification
     *
     * @var AuthenticationUser
     */
    public AuthenticationUser $authenticationUser;

    public function __construct(AuthenticationUser $authenticationUser)
    {
        $this->authenticationUser = $authenticationUser;
    }

}
