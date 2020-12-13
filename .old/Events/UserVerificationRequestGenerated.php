<?php

namespace App\Events;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * A user verification process has been requested.
 *
 * Fired when a new user registers and needs to verify their email.
 */
class UserVerificationRequestGenerated extends Registered
{
    use Dispatchable;

}
