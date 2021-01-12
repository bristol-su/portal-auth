<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class AlreadyRegisteredMessage extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::input($this->inputName())
            ->inputType('text')
            ->label('Already Registered Message')
            ->default($this->defaultValue())
            ->hint('The message to show a user on registration when they\'ve already registered')
            ->getSchema();
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
