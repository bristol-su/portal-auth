<?php


namespace App\Settings\Authentication\Attributes;


use BristolSU\Support\Settings\Definition\Definition;

class IdentifierLabel extends Definition
{

    public static function key(): string
    {
        return 'Authentication.Attributes.IdentifierLabel';
    }

    public static function defaultValue()
    {
        return 'Email';
    }
}
