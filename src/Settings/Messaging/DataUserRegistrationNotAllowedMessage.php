<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class DataUserRegistrationNotAllowedMessage extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Data User Registration - Error Message')
            ->setValue($this->defaultValue())
            ->setHint('The message to show to a user when they haven\'t been able to register because data registration is disabled');
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
