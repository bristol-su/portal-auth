<?php

namespace BristolSU\Auth\Social;

class NameParser
{

    public function __construct(protected string $name)
    {
    }

    protected function getParts(): array
    {
        return explode(' ', $this->name);
    }

    public function getFirstName(): string
    {
        $parts = $this->getParts();
        if(count($parts) > 0) {
            return $parts[0];
        }
        return '';
    }

    public function getLastName(): string
    {
        $parts = $this->getParts();
        if(count($parts) > 1) {
            unset($parts[0]);
            return implode(' ', $parts);
        }
        return '';
    }

    public static function parse(string $name)
    {
        return new static($name);
    }

}
