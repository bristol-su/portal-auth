<?php

namespace BristolSU\Auth\Settings\Credentials;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class PasswordValidation extends GlobalSetting
{

    public function rules(): array
    {
        return [$this->inputName() => [
            'string'
        ]];
    }

    public function key(): string
    {
        return 'authentication.credentials.password_validation';
    }

    public function defaultValue()
    {
        return 'required|min:6';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Password Validation')
            ->setValue($this->defaultValue())
            ->setHint('Validation for the password field')
            ->setTooltip('Make use of the Laravel validation tools.');
    }
}
