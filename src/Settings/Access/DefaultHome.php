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
        return \FormSchema\Generator\Field::textInput($this->inputName())
            ->setLabel('Route Home')
            ->setValue($this->defaultValue())
            ->setHint('The default route to send logged in users to')
            ->setTooltip('This must be the name of a route');
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

    /**
     * Get the value as a route name
     *
     * @param int|null $userId
     * @return string
     */
    public static function getValueAsRouteName(int $userId = null): string
    {
        return static::getValue($userId);
    }

    public static function getValueAsPath(int $userId = null, bool $absolute = false): string
    {
        return url()->route(static::getValueAsRouteName($userId), [], $absolute);
    }
}
