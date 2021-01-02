<?php


namespace BristolSU\Auth\Settings\Access;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class ControlUserRegistrationEnabled extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::checkBox($this->inputName())
            ->label('Can control users register?')
            ->default($this->defaultValue())
            ->hint('Allow control users to register to allow anyone to create an account')
            ->help('A control user is the main users people are aware of. If control users can\'t register, only '
                . 'those in control can register. This gives you complete control over who has an account and '
                . 'who doesn\'t, unlike the data user which just restricts the email/username.')
            ->getSchema();
    }

    public function defaultValue()
    {
        return true;
    }

    public function rules(): array
    {
        return [$this->inputName() => [
            'required',
            'boolean'
        ]];
    }

    public function key(): string
    {
        return 'authentication.access.controluser-registration-enabled';
    }
}
