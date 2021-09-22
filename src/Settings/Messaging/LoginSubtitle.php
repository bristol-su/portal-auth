<?php


namespace BristolSU\Auth\Settings\Messaging;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class LoginSubtitle extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Login Page Subtitle')
            ->setValue($this->defaultValue())
            ->setHint('The subtitle to show to the user on the login page');
    }

    public function defaultValue()
    {
        return 'Please login here';
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
        return 'authentication.messaging.login-subtitle';
    }
}
