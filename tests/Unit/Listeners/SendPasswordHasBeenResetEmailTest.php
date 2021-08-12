<?php

namespace BristolSU\Auth\Tests\Unit\Listeners;

use BristolSU\Auth\Events\PasswordHasBeenReset as PasswordHasBeenResetEvent;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Listeners\SendPasswordHasBeenResetEmail;
use BristolSU\Auth\Listeners\SendVerificationEmail;
use BristolSU\Auth\Notifications\PasswordHasBeenReset as PasswordHasBeenResetNotification;
use BristolSU\Auth\Notifications\VerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Contracts\Notifications\Dispatcher;
use Prophecy\Argument;

class SendPasswordHasBeenResetEmailTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $dispatcher = $this->prophesize(Dispatcher::class);

        $listener = new SendPasswordHasBeenResetEmail($dispatcher->reveal());

        $this->assertInstanceOf(SendPasswordHasBeenResetEmail::class, $listener);
    }

    /** @test */
    public function the_dispatcher_sends_the_notification_to_the_correct_user(){
        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->send(
            Argument::that(
                fn(AuthenticationUser $dispatchedTo): bool => $dispatchedTo->controlUser()->data()->email() === 'something@example.com'
            ),
            Argument::type(PasswordHasBeenResetNotification::class),
        )->shouldBeCalled();

        $dataUser = DataUser::factory()->create(['email' => 'something@example.com']);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);

        $event = new PasswordHasBeenResetEvent($user);

        $listener = new SendPasswordHasBeenResetEmail($dispatcher->reveal());
        $listener->handle($event);
    }

    /** @test */
    public function nothing_is_dispatched_if_the_user_email_is_null(){
        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->send(
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $dataUser = DataUser::factory()->create(['email' => null]);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);

        $event = new PasswordHasBeenResetEvent($user);

        $listener = new SendPasswordHasBeenResetEmail($dispatcher->reveal());
        $listener->handle($event);
    }

}
