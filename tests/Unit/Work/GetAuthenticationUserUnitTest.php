<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\Auth\Work\GetAuthenticationUserUnit;
use BristolSU\Auth\Work\RegisterAuthenticationUserUnit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class GetAuthenticationUserUnitTest extends TestCase
{

    /** @test */
    public function it_returns_the_register_result_if_the_auth_user_cannot_be_found(){
        $controlUser = $this->newUser();
        $authUser = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromControlId($controlUser->id())->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterAuthenticationUserUnit::class);
        $registerUnit->do($controlUser)->shouldBeCalled()->willReturn($authUser);

        $getUnit = new GetAuthenticationUserUnit($registerUnit->reveal(), $userRepository->reveal());
        $resolvedAuthUser = $getUnit->do($controlUser);

        $this->assertInstanceOf(AuthenticationUser::class, $resolvedAuthUser);
        $this->assertTrue($resolvedAuthUser->exists);
        $this->assertTrue($authUser->is($resolvedAuthUser));
    }

    /** @test */
    public function it_throws_a_validation_exception_with_errors_if_the_user_was_found(){
        AlreadyRegisteredMessage::setValue('You are already registered. This is a test message.');

        $controlUser = $this->newUser();
        $authUser = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromControlId($controlUser->id())->shouldBeCalled()->willReturn($authUser);

        $registerUnit = $this->prophesize(RegisterAuthenticationUserUnit::class);
        $registerUnit->do($controlUser)->shouldNotBeCalled();

        $getUnit = new GetAuthenticationUserUnit($registerUnit->reveal(), $userRepository->reveal());

        $exceptionWasThrown = false;
        try {
            $resolvedAuthUser = $getUnit->do($controlUser);
        } catch (ValidationException $e) {
            $exceptionWasThrown = true;

            $this->assertEquals(
                ['identifier' => [
                    'You are already registered. This is a test message.']
                ],
                $e->errors()
            );
        }

        $this->assertTrue($exceptionWasThrown);
    }

}
