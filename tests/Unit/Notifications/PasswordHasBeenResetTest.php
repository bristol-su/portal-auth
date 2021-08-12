<?php

namespace BristolSU\Auth\Tests\Unit\Notifications;

use BristolSU\Auth\Notifications\PasswordHasBeenReset;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class PasswordHasBeenResetTest extends TestCase
{

    /** @test */
    public function via_returns_mail_if_the_user_has_an_email_address(){
        $dataUser = DataUser::factory()->create(['email' => 'abc123@example.com']);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $notification = new PasswordHasBeenReset();
        $via = $notification->via($user);
        $this->assertEquals([
            'mail'
        ], $via);
    }

    /** @test */
    public function via_returns_an_empty_array_if_the_user_does_not_have_an_email_address(){
        $dataUser = DataUser::factory()->create(['email' => null]);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $notification = new PasswordHasBeenReset();
        $via = $notification->via($user);
        $this->assertEquals([], $via);
    }

    /** @test */
    public function toMail_returns_a_complete_mail_message(){
        $dataUser = DataUser::factory()->create(['email' => 'example@portal.com']);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $notification = new PasswordHasBeenReset();
        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertIsString($mailMessage->subject);
    }

}
