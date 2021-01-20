<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\Work\GetDataUserUnit;
use BristolSU\Auth\Work\RegisterDataUserUnit;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Repositories\DataUser as DataUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Prophecy\Argument;

class GetDataUserUnitTest extends TestCase
{

    /** @test */
    public function it_throws_a_validation_exception_with_errors_if_the_data_user_could_not_be_found_and_registration_is_disabled(){
        DataUserRegistrationEnabled::setValue(false);
        DataUserRegistrationNotAllowedMessage::setValue('Test message - registration not allowed');

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['email' => 'test@example.com'])->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(['email' => 'test@example.com'])->shouldNotBeCalled();

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $exceptionWasThrown = false;
        try {
            $resolvedDataUser = $getUnit->do('test@example.com', 'email', []);
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
    public function it_returns_the_registerUnit_result_if_the_data_user_was_not_found_but_registration_is_enabled(){
        DataUserRegistrationEnabled::setValue(true);

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['email' => 'test@example.com'])->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(['email' => 'test@example.com'])->shouldBeCalled()->willReturn($dataUser);

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $resolvedDataUser = $getUnit->do('test@example.com', 'email', []);
        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));
    }

    /** @test */
    public function it_passes_the_identifier_and_extra_params_to_the_registerUnit_result_if_the_data_user_was_not_found_but_registration_is_enabled(){
        DataUserRegistrationEnabled::setValue(true);

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['email' => 'test@example.com'])->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(['email' => 'test@example.com', 'name' => 'Toby Test', 'firstName' => 'Toby'])->shouldBeCalled()->willReturn($dataUser);

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $resolvedDataUser = $getUnit->do('test@example.com', 'email', ['name' => 'Toby Test', 'firstName' => 'Toby']);
        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));
    }

    /** @test */
    public function the_identifier_value_is_taken_from_settings(){
        DataUserRegistrationEnabled::setValue(true);
        IdentifierAttribute::setValue('this-is-a-test');

        $dataUser = factory(DataUser::class)->create();

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['this-is-a-test' => 'and-a-value'])->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(['this-is-a-test' => 'and-a-value'])->shouldBeCalled()->willReturn($dataUser);

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $resolvedDataUser = $getUnit->do('and-a-value');
        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));
    }

    /** @test */
    public function the_identifier_value_is_overridden_by_identifierKey(){
        DataUserRegistrationEnabled::setValue(true);
        IdentifierAttribute::setValue('this-is-a-test-from-the-settings');

        $dataUser = factory(DataUser::class)->create();

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['this-is-a-test' => 'and-a-value'])->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(['this-is-a-test' => 'and-a-value'])->shouldBeCalled()->willReturn($dataUser);

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $resolvedDataUser = $getUnit->do('and-a-value', 'this-is-a-test');
        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));
    }

    /** @test */
    public function it_returns_the_data_user_if_found_by_the_repository(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['email' => 'test@example.com'])->shouldBeCalled()->willReturn($dataUser);

        $registerUnit = $this->prophesize(RegisterDataUserUnit::class);
        $registerUnit->do(Argument::any())->shouldNotBeCalled();

        $getUnit = new GetDataUserUnit($registerUnit->reveal(), $dataUserRepository->reveal());

        $resolvedDataUser = $getUnit->do('test@example.com', 'email', []);
        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));
    }


}
