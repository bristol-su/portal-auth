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
        return \FormSchema\Generator\Field::checkBox($this->inputName())
            ->label('Email Verification Required?')
            ->default($this->defaultValue())
            ->hint('Should a user have to verify their email address?')
            ->help('We highly recommend turning this on provide security when registering.')
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
}
