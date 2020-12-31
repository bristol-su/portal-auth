<?php

namespace BristolSU\Auth\Settings\Login;

use BristolSU\Support\Settings\Definition\Group;

class LoginGroup extends Group
{

    public function key(): string
    {
        return 'authentication.login';
    }

    public function name(): string
    {
        return 'Login';
    }

    public function description(): string
    {
        return 'Set up the ways users can log into the site';
    }
}
