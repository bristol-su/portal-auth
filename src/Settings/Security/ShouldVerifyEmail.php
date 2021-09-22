<?php

namespace BristolSU\Auth\Settings\Security;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class ShouldVerifyEmail extends GlobalSetting
{

    public function key(): string
    {
        return 'authentication.security.should-verify-email';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::switch($this->inputName())
            ->setLabel('Email Verification Required?')
            ->setValue($this->defaultValue())
            ->setOnText('Verification required')
            ->setOffText('Verification not required')
            ->setHint('Should a user have to verify their email address?')
            ->setTooltip('We highly recommend turning this on provide security when registering.');
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
}
