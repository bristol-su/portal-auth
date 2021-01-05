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

class VerifyEmail extends Notification implements ShouldQueue
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
            ->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('If you did not create an account, no further action is required.'));
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
            app(UrlGenerator::class)->route('verify'),
            ['id' => $user->id],
            '+' . Config::get('auth.verification.expire', 60) . ' minutes',
            1
        )->getFullUrl();
    }

}
