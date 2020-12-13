<?php

namespace App\Notifications;

use BristolSU\Support\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Linkeys\UrlSigner\Contracts\Models\Link;

class VerifyEmail extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $user
     * @return array
     */
    public function via(User $user)
    {
        // TODO Implement additional notification channels
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $user)
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
    protected function verificationUrl(User $user)
    {
        return \Linkeys\UrlSigner\Facade\UrlSigner::sign(
            app(UrlGenerator::class)->route('verification.verify'),
            ['id' => $user->id],
            '+' . Config::get('auth.verification.expire', 60) . ' minutes',
            1
        )->getFullUrl();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $user
     * @return array
     */
    public function toArray(User $user)
    {
        return [
            //
        ];
    }
}
