<?php

namespace BristolSU\Auth\Settings\Security;

use BristolSU\Support\Settings\Definition\Group;

class SecurityGroup extends Group
{

    public function key(): string
    {
        return 'authentication.security';
    }

    public function name(): string
    {
        return 'Security';
    }

    public function description(): string
    {
        return 'Secure your site';
    }
}
