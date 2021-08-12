<?php

namespace BristolSU\Auth\Tests\Unit\Listeners;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Listeners\SendVerificationEmail;
use BristolSU\Auth\Notifications\VerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Contracts\Notifications\Dispatcher;
use Prophecy\Argument;

class SendVerificationEmailTest extends TestCase
{

    /** @test */
    public function handle_dispatches_an_instance_of_the_verify_email_mailable(){
        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->send(
            Argument::type(AuthenticationUser::class),
            Argument::type(VerifyEmail::class),
        )->shouldBeCalled();

        $dataUser = DataUser::factory()->create(['email' => 'something@example.com']);
        $controlUser = User::factory()->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);

        $event = new UserVerificationRequestGenerated($user);

        $listener = new SendVerificationEmail($dispatcher->reveal());
        $listener->handle($event);
    }

    /** @test */
    public function handle_doesnt_dispatch_the_mail_if_an_email_is_not_set_on_the_user(){
        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->send(
            Argument::type(AuthenticationUser::class),
            Argument::type(VerifyEmail::class),
        )->shouldNotBeCalled();

        $dataUser = DataUser::factory()->create(['email' => null]);
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser]);

        $event = new UserVerificationRequestGenerated($user);

        $listener = new SendVerificationEmail($dispatcher->reveal());
        $listener->handle($event);
    }

    /** @test */
    public function handle_doesnt_dispatch_the_mail_if_the_user_has_verified_their_email_address(){
        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher->send(
            Argument::type(AuthenticationUser::class),
            Argument::type(VerifyEmail::class),
        )->shouldNotBeCalled();

        $dataUser = DataUser::factory()->create(['email' => 'example@test.com']);
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser]);

        $event = new UserVerificationRequestGenerated($user);

        $listener = new SendVerificationEmail($dispatcher->reveal());
        $listener->handle($event);
    }

}
