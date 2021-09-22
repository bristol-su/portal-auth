<?php


namespace BristolSU\Auth\Settings\Access;


use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class DataUserRegistrationEnabled extends GlobalSetting
{

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::checkBox($this->inputName())
            ->setLabel('Can data users register?')
            ->setValue($this->defaultValue())
            ->setHint('Allow data users to register to allow any email address to create an account')
            ->setTooltip('A data user holds personal data about your users, for example their name or email ' .
                'address. If data users can\'t register, only users with emails already in your data store will be able to register.');
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
        return 'authentication.access.datauser-registration-enabled';
    }
}
