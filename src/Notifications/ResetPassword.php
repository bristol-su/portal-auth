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

class ResetPassword extends Notification implements ShouldQueue
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
        $verificationUrl = $this->verificationUrl($user);

        return (new MailMessage)
            ->subject(Lang::get('Reset Password'))
            ->line(Lang::get('Please click the button below to reset your password.'))
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('If you did not ask to reset your password, you should go ahead and change it.'))
            ->line(Lang::get('Please do not forward or share this email to anyone.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $user
     * @return string
     */
    protected function verificationUrl(AuthenticationUser $user)
    {
        return \Linkeys\UrlSigner\Facade\UrlSigner::sign(
            app(UrlGenerator::class)->route('password.reset'),
            ['user_id' => $user->id],
            '+60 minutes',
            1
        )->getFullUrl();
    }

}
