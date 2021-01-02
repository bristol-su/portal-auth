<?php

namespace BristolSU\Auth\Settings\Security;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class PasswordConfirmationTimeout extends GlobalSetting
{

    public function key(): string
    {
        return 'authentication.security.password-confirmation-timeout';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::input($this->inputName())
            ->inputType('number')
            ->label('Password Confirmation Timeout')
            ->default($this->defaultValue())
            ->hint('How long to grant access to secure areas of the site before requiring a password confirmation, in seconds.')
            ->help('We\'d recommend about 30 minutes (or 1800 seconds), but it can be made shorter for increased security or longer for ease of use')
            ->getSchema();
    }

    public function defaultValue()
    {
        return 1800;
    }

    public function rules(): array
    {
        return [$this->inputName() => [
            'required',
            'integer',
            'min:1',
            'max:86400'
        ]];
    }
}
