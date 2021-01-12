<?php

namespace BristolSU\Auth\Tests\Unit\Events;

use BristolSU\Auth\Events\PasswordResetRequestGenerated;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;

class PasswordResetRequestGeneratedTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $user = AuthenticationUser::factory()->create();
        $event = new PasswordResetRequestGenerated($user);

        $this->assertInstanceOf(PasswordResetRequestGenerated::class, $event);
    }

    /** @test */
    public function the_authentication_user_can_be_retrieved(){
        $user = AuthenticationUser::factory()->create();
        $event = new PasswordResetRequestGenerated($user);

        $this->assertModelEquals($user, $event->authenticationUser);
    }

}
