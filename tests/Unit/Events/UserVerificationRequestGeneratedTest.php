<?php

namespace BristolSU\Auth\Tests\Unit;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;

class UserVerificationRequestGeneratedTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $user = AuthenticationUser::factory()->create();
        $event = new UserVerificationRequestGenerated($user);

        $this->assertInstanceOf(UserVerificationRequestGenerated::class, $event);
    }

    /** @test */
    public function the_authentication_user_can_be_retrieved(){
        $user = AuthenticationUser::factory()->create();
        $event = new UserVerificationRequestGenerated($user);

        $this->assertModelEquals($user, $event->authenticationUser);
    }

}
