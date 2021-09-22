<?php

namespace BristolSU\Auth\Settings\Credentials;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class IdentifierAttributeValidation extends GlobalSetting
{

    public function rules(): array
    {
        return [$this->inputName() => [
            'string'
        ]];
    }

    public function key(): string
    {
        return 'authentication.credentials.identifier_validation';
    }

    public function defaultValue()
    {
        return 'required|email';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Identifier Validation')
            ->setValue($this->defaultValue())
            ->setHint('Validation for the identifier attribute')
            ->setTooltip('Make use of the Laravel validation tools.');
    }
}
