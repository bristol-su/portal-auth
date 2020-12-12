<?php

namespace BristolSU\Auth\Tests\Unit\User;

use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\AuthenticationUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthenticationUserRepositoryTest extends TestCase
{


    /** @test */
    public function getFromControlId_gets_the_user_with_the_control_id()
    {
        $user = AuthenticationUser::factory()->create();

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->getFromControlId($user->control_id);
        $this->assertInstanceOf(AuthenticationUser::class, $resolvedUser);
        $this->assertModelEquals($user, $resolvedUser);
    }

    /** @test */
    public function getFromControlId_throws_an_exception_if_no_user_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->getFromControlId(6);
    }

    /** @test */
    public function create_creates_a_user()
    {
        $userParams = [
            'control_id' => 1,
        ];

        $userRepository = new AuthenticationUserRepository();
        $user = $userRepository->create($userParams);

        $this->assertInstanceOf(AuthenticationUser::class, $user);
        $this->assertEquals(1, $user->controlId());

        $this->assertDatabaseHas('authentication_users', $userParams);
    }

    /** @test */
    public function all_gets_all_users()
    {
        $users = AuthenticationUser::factory()->count(10)->create();

        $userRepository = new AuthenticationUserRepository();
        $allUsers = $userRepository->all();

        foreach($users as $user) {
            $this->assertModelEquals($user, $allUsers->shift());
        }
    }

    /** @test */
    public function getFromRememberToken_gets_the_user_with_the_control_id()
    {
        $user = AuthenticationUser::factory()->create(['remember_token' => 'abc123']);

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->getFromRememberToken('abc123');
        $this->assertInstanceOf(AuthenticationUser::class, $resolvedUser);
        $this->assertModelEquals($user, $resolvedUser);
    }

    /** @test */
    public function getFromRememberToken_throws_an_exception_if_no_user_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->getFromrememberToken('abc1234');
    }

    /** @test */
    public function getById_returns_a_user_by_id(){
        $user = AuthenticationUser::factory()->create(['id' => 1]);

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->getById(1);
        $this->assertInstanceOf(AuthenticationUser::class, $resolvedUser);
        $this->assertModelEquals($user, $resolvedUser);
    }

    /** @test */
    public function setRememberToken_sets_the_remember_token_of_the_user()
    {
        $user = AuthenticationUser::factory()->create(['remember_token' => 'abc123']);

        $userRepository = new AuthenticationUserRepository();
        $resolvedUser = $userRepository->setRememberToken($user->id, 'abc1234');

        $this->assertDatabaseHas('authentication_users', [
            'id' => $user->id, 'remember_token' => 'abc1234'
        ]);
    }
}
