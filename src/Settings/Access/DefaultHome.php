<?php

namespace BristolSU\Auth\Settings\Access;

use BristolSU\Support\Settings\Definition\UserSetting;
use FormSchema\Schema\Field;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Facades\Route;

class DefaultHome extends UserSetting
{

    public function key(): string
    {
        return 'authentication.access.default-home';
    }

    public function defaultValue()
    {
        return 'portal';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::input($this->inputName())
            ->inputType('text')
            ->label('Route Home')
            ->default($this->defaultValue())
            ->hint('The default route to send logged in users to')
            ->help('This must be the name of a route')
            ->getSchema();
    }

    public function rules(): array
    {
        return [
            $this->inputName() => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if(!Route::has($value)) {
                        $fail(sprintf('The default home is not a valid route name.'));
                    }
                }
            ]
        ];
    }
}
