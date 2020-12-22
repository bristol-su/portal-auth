<?php

namespace BristolSU\Auth\Settings\Credentials;

use BristolSU\Support\Settings\Definition\Group;

class CredentialsGroup extends Group
{

    public function key(): string
    {
        return 'authentication.credentials';
    }

    public function name(): string
    {
        return 'Credentials';
    }

    public function description(): string
    {
        return 'Settings related to users login credentials';
    }
}
