<?php

namespace BristolSU\Auth\Listeners;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Notifications\Dispatcher;

class SendVerificationEmail
{

    public function handle(UserVerificationRequestGenerated $event)
    {
        if($event->authenticationUser->controlUser()->data()->email() !== null) {
            app(Dispatcher::class)->send($event->authenticationUser, new VerifyEmail());
        }
    }

}
