<?php

namespace BristolSU\Auth\Tests\Unit\Work;

use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\Work\RegisterControlUserUnit;
use BristolSU\ControlDB\Contracts\Repositories\User as ControlUserRepository;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;

class RegisterControlUserUnitTest extends TestCase
{

    /** @test */
    public function it_creates_and_returns_a_control_user_with_the_right_user_id(){
        $dataUser = DataUser::factory()->create();
        $controlUser = $this->newUser(['data_provider_id' => $dataUser->id()]);

        $controlUserRepository = $this->prophesize(ControlUserRepository::class);
        $controlUserRepository->create($dataUser->id())->shouldBeCalled()->willReturn($controlUser);

        $registerUnit = new RegisterControlUserUnit($controlUserRepository->reveal());
        $resolvedControlUser = $registerUnit->do($dataUser);

        $this->assertInstanceOf(User::class, $resolvedControlUser);
        $this->assertTrue($resolvedControlUser->exists);
        $this->assertTrue($controlUser->is($resolvedControlUser));

        $this->assertDatabaseHas('control_users', [
            'id' => $controlUser->id(),
            'data_provider_id' => $dataUser->id()
        ]);

    }

}
