<?php


namespace BristolSU\Auth\Settings\Access;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class RegistrationEnabled extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::checkBox($this->inputName())
            ->label('Enable Registration')
            ->default($this->defaultValue())
            ->hint('Allow users to register to the site')
            ->help('You can still control who is allowed to register using other controls. This turns off registration for everyone')
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
        return 'authentication.access.registration-enabled';
    }
}
