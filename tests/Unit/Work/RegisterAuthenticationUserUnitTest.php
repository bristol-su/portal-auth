<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\Auth\Work\RegisterAuthenticationUserUnit;

class RegisterAuthenticationUserUnitTest extends TestCase
{

    /** @test */
    public function it_creates_and_returns_an_authentication_user_with_the_right_control_id(){
        $controlUser = $this->newUser();
        $authUser = AuthenticationUser::factory()->create([
            'control_id' => $controlUser->id()
        ]);

        $authenticationUserRepository = $this->prophesize(AuthenticationUserRepository::class);
        $authenticationUserRepository->create(['control_id' => $controlUser->id()])->shouldBeCalled()->willReturn($authUser);

        $registerUnit = new RegisterAuthenticationUserUnit($authenticationUserRepository->reveal());
        $resolvedAuthUser = $registerUnit->do($controlUser);

        $this->assertInstanceOf(AuthenticationUser::class, $resolvedAuthUser);
        $this->assertTrue($resolvedAuthUser->exists);
        $this->assertTrue($authUser->is($resolvedAuthUser));

        $this->assertDatabaseHas('authentication_users', [
            'id' => $authUser->id(),
            'control_id' => $controlUser->id()
        ]);

    }

}
