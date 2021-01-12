<?php

namespace BristolSU\Auth\Notifications;

use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class PasswordHasBeenReset extends Notification implements ShouldQueue
{
    use Queueable, Notifiable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $user
     * @return array
     */
    public function via(AuthenticationUser $user)
    {
        $channels = [];
        if($user->controlUser()->data()->email() !== null) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $user
     * @return MailMessage
     */
    public function toMail(AuthenticationUser $user)
    {
        return (new MailMessage)
            ->subject(Lang::get('Your password has been reset'))
            ->line(Lang::get('Your password has been changed.'))
            ->line(Lang::get('If you did not change your password, please contact us.'));
    }

}
