<?php

namespace BristolSU\Auth\Settings;

use BristolSU\Support\Settings\Definition\Category;

class AuthCategory extends Category
{

    public function key(): string
    {
        return 'authentication';
    }

    public function name(): string
    {
        return 'Authentication';
    }

    public function description(): string
    {
        return 'Settings related to user authentication';
    }
}
