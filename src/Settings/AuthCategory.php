<?php

namespace BristolSU\Auth\Settings;

use BristolSU\Support\Settings\Definition\Category;

class AuthCategory extends Category
{

    public function name(): string
    {
        return 'Authentication';
    }

    public function icon(): string
    {
        return 'lock';
    }
}
