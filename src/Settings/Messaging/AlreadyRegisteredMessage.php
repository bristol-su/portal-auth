<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class AlreadyRegisteredMessage extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Already Registered Message')
            ->setValue($this->defaultValue())
            ->setHint('The message to show a user on registration when they\'ve already registered');
    }

    public function defaultValue()
    {
        return 'You have already registered!';
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
        return 'authentication.messaging.already-registered';
    }
}
