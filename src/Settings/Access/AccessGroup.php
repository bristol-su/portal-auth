<?php

namespace BristolSU\Auth\Settings\Access;

use BristolSU\Support\Settings\Definition\Group;

class AccessGroup extends Group
{

    public function key(): string
    {
        return 'authentication.access';
    }

    public function name(): string
    {
        return 'Access';
    }

    public function description(): string
    {
        return 'Set up who can log into the site and how';
    }
}
