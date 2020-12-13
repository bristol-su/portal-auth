<?php


namespace App\Settings\Authentication\Attributes;


use BristolSU\Support\Settings\Definition\Definition;

class Identifier extends Definition
{

    public static function key(): string
    {
        return 'Authentication.Attributes.Identifier';
    }

    public static function defaultValue()
    {
        return 'email';
    }
}
