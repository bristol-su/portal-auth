<?php

namespace BristolSU\Auth\Listeners;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Notifications\Dispatcher;

class SendVerificationEmail
{

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(UserVerificationRequestGenerated $event)
    {
        if($event->authenticationUser->controlUser()->data()->email() !== null && !$event->authenticationUser->hasVerifiedEmail()) {
            $this->dispatcher->send($event->authenticationUser, new VerifyEmail());
        }
    }

}
