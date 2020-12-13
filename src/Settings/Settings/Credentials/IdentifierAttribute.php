<?php

namespace BristolSU\Auth\Settings\Settings\Credentials;

use BristolSU\Support\Settings\Definition\Definition;

class IdentifierAttribute extends Definition
{

    public static function key(): string
    {
        return 'Authentication.Credentials.Identifier';
    }

    public static function defaultValue()
    {
        return 'email';
    }
}
