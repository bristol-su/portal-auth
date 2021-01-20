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
        return sprintf('%s Driver', $this->formatAsTitle($this->driver()));
    }

    public function description(): string
    {
        return sprintf('Set up the authentication integration with %s.', $this->formatAsTitle($this->driver()));
    }

    abstract public function driver(): string;

    protected function formatAsTitle(string $driver)
    {
        return Str::title(
            str_replace(['-', '_'], ' ', $driver)
        );
    }
}
