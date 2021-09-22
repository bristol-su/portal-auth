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
        return \FormSchema\Generator\Field::switch($this->inputName())
            ->setLabel('Login enabled?')
            ->setValue($this->defaultValue())
            ->setHint(sprintf('Should users be able to log in through %s', $this->driver()))
            ->setTooltip('Make sure you set up the client ID and secret before enabling this')
            ->setOnText('Enabled')
            ->setOffText('Disabled');
    }

    abstract public function driver(): string;

}
