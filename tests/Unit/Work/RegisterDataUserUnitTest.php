<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\Work\RegisterDataUserUnit;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;
use BristolSU\ControlDB\Models\DataUser;

class RegisterDataUserUnitTest extends TestCase
{

    /** @test */
    public function it_creates_the_data_user_with_the_given_parameters_or_null_if_not_given(){
        $dataUser = DataUser::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'Toby',
            'last_name' => 'Twigger',
            'preferred_name' => null,
            'dob' => null
        ]);

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->create('Toby', 'Twigger', 'test@example.com', null, null)->shouldBeCalled()->willReturn($dataUser);

        $registerUnit = new RegisterDataUserUnit($dataUserRepository->reveal());
        $resolvedDataUser = $registerUnit->do([
            'email' => 'test@example.com',
            'first_name' => 'Toby',
            'last_name' => 'Twigger'
        ]);

        $this->assertInstanceOf(DataUser::class, $resolvedDataUser);
        $this->assertTrue($resolvedDataUser->exists);
        $this->assertTrue($dataUser->is($resolvedDataUser));

        $this->assertDatabaseHas('control_data_user', [
            'id' => $dataUser->id(),
            'email' => 'test@example.com',
            'first_name' => 'Toby',
            'last_name' => 'Twigger',
            'preferred_name' => null,
            'dob' => null
        ]);
    }

}
