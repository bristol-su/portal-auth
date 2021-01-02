<?php

namespace BristolSU\Auth\Settings\Messaging;

class MessagingGroup
{

    public function key(): string
    {
        return 'authentication.messaging';
    }

    public function name(): string
    {
        return 'Messaging';
    }

    public function description(): string
    {
        return 'Set up messages the user sees during login';
    }

}
