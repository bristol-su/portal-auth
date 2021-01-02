<?php


namespace BristolSU\Auth\Settings\Access;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class ControlUserRegistrationNotAllowedMessage extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::input($this->inputName())
            ->inputType('text')
            ->label('Control User Registration - Error Message')
            ->default($this->defaultValue())
            ->hint('The message to show to a user when they haven\'t been able to register because control registration is disabled')
            ->getSchema();
    }

    public function defaultValue()
    {
        return 'Registration is currently closed to new users.';
    }

    public function rules(): array
    {
        return [$this->inputName() => [
            'required',
            'string'
        ]];
    }

    public function key(): string
    {
        return 'authentication.access.controluser-registration-not-allowed-message';
    }
}
