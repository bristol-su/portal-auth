<?php

namespace BristolSU\Auth\Tests\Unit\Events;

use BristolSU\Auth\Events\PasswordHasBeenReset;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;

class PasswordHasBeenResetTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $user = AuthenticationUser::factory()->create();
        $event = new PasswordHasBeenReset($user);

        $this->assertInstanceOf(PasswordHasBeenReset::class, $event);
    }

    /** @test */
    public function the_authentication_user_can_be_retrieved(){
        $user = AuthenticationUser::factory()->create();
        $event = new PasswordHasBeenReset($user);

        $this->assertModelEquals($user, $event->authenticationUser);
    }

}
