<?php

namespace BristolSU\Auth\Settings\Credentials;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;

class IdentifierAttribute extends GlobalSetting
{

    public function rules(): array
    {
        return [
            'string'
        ];
    }

    public function key(): string
    {
        return 'authentication.credentials.identifier';
    }

    public function defaultValue()
    {
        return 'email';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::select($this->inputName())
            ->label('Identifier')
            ->default($this->defaultValue())
            ->hint('The attribute users can use to log in')
            ->help('You can choose multiple fields to allow users to log in with any field')
            ->values([
                ['id' => 'email', 'name' => 'Email Address']
            ])
            ->getSchema();
    }
}
