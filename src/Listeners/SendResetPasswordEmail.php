<?php

namespace BristolSU\Auth\Listeners;

use BristolSU\Auth\Events\PasswordResetRequestGenerated;
use BristolSU\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Notifications\Dispatcher;

class SendResetPasswordEmail
{

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(PasswordResetRequestGenerated $event)
    {
        if($event->authenticationUser->controlUser()->data()->email() !== null) {
            $this->dispatcher->send($event->authenticationUser, new ResetPassword());
        }
    }

}
