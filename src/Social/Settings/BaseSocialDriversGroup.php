<?php

namespace BristolSU\Auth\Social\Settings;

use BristolSU\Support\Settings\Definition\Group;
use Illuminate\Support\Str;

abstract class BaseSocialDriversGroup extends Group
{

    public function key(): string
    {
        return sprintf('social-drivers.%s', Str::lower($this->driver()));
    }

    public function name(): string
    {
        return sprintf('%s Driver', Str::title($this->driver()));
    }

    public function description(): string
    {
        return sprintf('Set up the authentication integration with %s.', Str::title($this->driver()));
    }

    abstract public function driver(): string;
}
