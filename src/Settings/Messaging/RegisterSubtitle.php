<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class RegisterSubtitle extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Register Page Subtitle')
            ->setValue($this->defaultValue())
            ->setHint('The subtitle to show to the user on the login page');
    }

    public function defaultValue()
    {
        return 'Please register here';
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
        return 'authentication.messaging.register-subtitle';
    }
}
