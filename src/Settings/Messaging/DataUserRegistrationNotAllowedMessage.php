<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class DataUserRegistrationNotAllowedMessage extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::input($this->inputName())
            ->inputType('text')
            ->label('Data User Registration - Error Message')
            ->default($this->defaultValue())
            ->hint('The message to show to a user when they haven\'t been able to register because data registration is disabled')
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
        return 'authentication.messaging.datauser-registration-not-allowed-message';
    }
}
