<?php

namespace BristolSU\Auth\Social\Settings;

use BristolSU\Support\Settings\Definition\Category;

class SocialDriversCategory extends Category
{

    public function key(): string
    {
        return 'social-drivers';
    }

    public function name(): string
    {
        return 'Social Drivers';
    }

    public function description(): string
    {
        return 'Set up third party sites users can log in through.';
    }
}
