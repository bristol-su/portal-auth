<?php

namespace BristolSU\Auth\Social\Settings\Providers;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;
use Illuminate\Support\Str;

abstract class BaseProviderEnabledSetting extends GlobalSetting
{

    public function rules(): array
    {
        return [
            $this->inputName() => [
                'required', 'boolean'
            ]
        ];
    }

    public function key(): string
    {
        return sprintf('social-drivers.%s.enabled', Str::lower($this->driver()));
    }

    public function defaultValue()
    {
        return false;
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::checkBox($this->inputName())
            ->label('Login enabled?')
            ->default($this->defaultValue())
            ->hint(sprintf('Should users be able to log in through %s', $this->driver()))
            ->help('Make sure you set up the client ID and secret before enabling this')
            ->getSchema();
    }

    abstract public function driver(): string;

}
