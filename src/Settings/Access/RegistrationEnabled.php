<?php


namespace BristolSU\Auth\Settings\Access;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class RegistrationEnabled extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::switch($this->inputName())
            ->setLabel('Enable Registration')
            ->setOffText('Cannot register')
            ->setOnText('Can register')
            ->setValue($this->defaultValue())
            ->setHint('Allow users to register to the site')
            ->setTooltip('You can still control who is allowed to register using other controls. This turns off registration for everyone');
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
