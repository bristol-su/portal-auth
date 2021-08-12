<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\Work\GetControlUserUnit;
use BristolSU\Auth\Work\RegisterControlUserUnit;
use BristolSU\ControlDB\Contracts\Repositories\User as ControlUserRepository;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class GetControlUserUnitTest extends TestCase
{

    /** @test */
    public function it_throws_a_validation_exception_with_errors_if_the_control_user_does_not_exist_and_registration_is_disabled(){
        ControlUserRegistrationEnabled::setValue(false);
        ControlUserRegistrationNotAllowedMessage::setValue('Test message - registration not allowed');

        $dataUser = DataUser::factory()->create();
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);

        $controlUserRepository = $this->prophesize(ControlUserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id())->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterControlUserUnit::class);
        $registerUnit->do($dataUser)->shouldNotBeCalled();

        $getUnit = new GetControlUserUnit($registerUnit->reveal(), $controlUserRepository->reveal());

        $exceptionWasThrown = false;
        try {
            $resolvedControlUser = $getUnit->do($dataUser);
        } catch (ValidationException $e) {
            $exceptionWasThrown = true;

            $this->assertEquals(
                ['identifier' => [
                    'Test message - registration not allowed']
                ],
                $e->errors()
            );
        }

        $this->assertTrue($exceptionWasThrown);
    }

    /** @test */
    public function it_returns_the_registerUnit_result_if_the_control_user_is_not_found_but_registration_is_enabled(){
        ControlUserRegistrationEnabled::setValue(true);

        $dataUser = DataUser::factory()->create();
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);

        $controlUserRepository = $this->prophesize(ControlUserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id())->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterControlUserUnit::class);
        $registerUnit->do($dataUser)->shouldBeCalled()->willReturn($controlUser);

        $getUnit = new GetControlUserUnit($registerUnit->reveal(), $controlUserRepository->reveal());

        $resolvedControlUser = $getUnit->do($dataUser);
        $this->assertInstanceOf(User::class, $resolvedControlUser);
        $this->assertTrue($resolvedControlUser->exists);
        $this->assertTrue($controlUser->is($resolvedControlUser));
    }

    /** @test */
    public function it_returns_the_control_user_if_it_already_exists(){
        $dataUser = DataUser::factory()->create();
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);

        $controlUserRepository = $this->prophesize(ControlUserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id())->shouldBeCalled()->willReturn($controlUser);

        $registerUnit = $this->prophesize(RegisterControlUserUnit::class);
        $registerUnit->do($dataUser)->shouldNotBeCalled();

        $getUnit = new GetControlUserUnit($registerUnit->reveal(), $controlUserRepository->reveal());

        $resolvedControlUser = $getUnit->do($dataUser);
        $this->assertInstanceOf(User::class, $resolvedControlUser);
        $this->assertTrue($resolvedControlUser->exists);
        $this->assertTrue($controlUser->is($resolvedControlUser));
    }
}
