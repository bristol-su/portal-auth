<?php

namespace BristolSU\Auth\Social\Settings\Providers;

use BristolSU\Support\Settings\Definition\GlobalSetting;
use FormSchema\Schema\Field;
use Illuminate\Support\Str;

abstract class BaseProviderClientIdSetting extends GlobalSetting
{

    protected bool $encrypt = true;

    public function rules(): array
    {
        return [$this->inputName() => [
            'required',
            'string',
            'min:3',
            'max:400'
        ]];
    }

    public function key(): string
    {
        return sprintf('social-drivers.%s.client_id', Str::lower($this->driver()));
    }

    public function defaultValue()
    {
        return null;
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Client ID');
    }

    abstract public function driver(): string;

}
