<?php

namespace BristolSU\Auth\Listeners;

use BristolSU\Auth\Events\PasswordHasBeenReset as PasswordHasBeenResetEvent;
use BristolSU\Auth\Notifications\PasswordHasBeenReset;
use Illuminate\Contracts\Notifications\Dispatcher;

class SendPasswordHasBeenResetEmail
{

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(PasswordHasBeenResetEvent $event)
    {
        if($event->authenticationUser->controlUser()->data()->email() !== null) {
            $this->dispatcher->send($event->authenticationUser, new PasswordHasBeenReset());
        }
    }

}
