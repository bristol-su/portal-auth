<?php

namespace BristolSU\Auth\Settings\Login;

use BristolSU\Support\Settings\Definition\UserSetting;
use FormSchema\Schema\Field;

class DefaultHome extends UserSetting
{

    public function key(): string
    {
        // TODO: Implement key() method.
    }

    public function defaultValue()
    {
        return 'portal';
    }

    public function fieldOptions(): Field
    {
        // TODO: Implement fieldOptions() method.
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
    }
}
