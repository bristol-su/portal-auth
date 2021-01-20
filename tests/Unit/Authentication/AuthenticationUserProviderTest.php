<?php

namespace BristolSU\Auth\Tests\Unit\Authentication;

use BristolSU\Auth\Authentication\AuthenticationUserProvider;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use BristolSU\Support\Settings\Saved\SavedSettingModel;
use BristolSU\Support\Settings\Saved\ValueManipulator\Manipulator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Prophecy\Argument;

class AuthenticationUserProviderTest extends TestCase
{
    /** @test */
    public function retrieveById_returns_the_user_with_the_given_id(){
        $user = AuthenticationUser::factory()->create();

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getById($user->id)->shouldBeCalled()->willReturn($user);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $resolvedUser = $provider->retrieveById($user->id);

        $this->assertModelEquals($user, $resolvedUser);
    }

    /** @test */
    public function retrieveById_returns_null_if_repository_throws_an_exception(){
        $user = AuthenticationUser::factory()->create();

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getById($user->id)->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $resolvedUser = $provider->retrieveById($user->id);

        $this->assertNull($resolvedUser);
    }

    /** @test */
    public function retrieveByToken_gets_a_user_by_remember_token(){
        $user = AuthenticationUser::factory()->create(['remember_token' => 'abc123']);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromRememberToken('abc123')->shouldBeCalled()->willReturn($user);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $resolvedUser = $provider->retrieveByToken($user->id, 'abc123');

        $this->assertInstanceOf(AuthenticationUser::class, $resolvedUser);
        $this->assertModelEquals($user, $resolvedUser);
    }

    /** @test */
    public function retrieveByToken_returns_null_if_the_user_with_the_given_token_has_a_different_id_to_the_given_identifier(){
        $user = AuthenticationUser::factory()->create(['remember_token' => 'abc123']);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromRememberToken('abc123')->shouldBeCalled()->willReturn($user);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $resolvedUser = $provider->retrieveByToken($user->id + 1, 'abc123');

        $this->assertNull($resolvedUser);
    }

    /** @test */
    public function retrieveByToken_returns_null_if_the_repository_throws_an_exception(){
        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromRememberToken('abc123')->shouldBeCalled()->willThrow(ModelNotFoundException::class);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $resolvedUser = $provider->retrieveByToken(1, 'abc123');

        $this->assertNull($resolvedUser);
    }

    /** @test */
    public function updateRememberToken_sets_the_remember_token_for_the_right_user(){
        $user = AuthenticationUser::factory()->create(['remember_token' => 'abc123']);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->setRememberToken(1, 'abc1234')->shouldBeCalled();

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $provider->updateRememberToken($user, 'abc1234');
    }

    /** @test */
    public function validateCredentials_returns_true_if_the_user_has_the_right_password(){
        $user = AuthenticationUser::factory()->create(['password' => Hash::make('secret123')]);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $this->assertTrue(
            $provider->validateCredentials($user, ['password' => 'secret123'])
        );
    }

    /** @test */
    public function validateCredentials_returns_true_if_the_user_has_the_wrong_password(){
        $user = AuthenticationUser::factory()->create(['password' => Hash::make('secret123')]);

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $this->assertFalse(
            $provider->validateCredentials($user, ['password' => 'secret1234'])
        );
    }

    /** @test */
    public function retrieveByCredentials_returns_null_if_the_identifier_key_not_given(){
        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $provider = new AuthenticationUserProvider($userRepository->reveal());

        $retrievedUser = $provider->retrieveByCredentials(['not_identifier' => 'abc123']);

        $this->assertNull($retrievedUser);
    }

    /** @test */
    public function it_returns_null_if_the_data_user_with_the_identifier_given_by_settings_is_not_found(){

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(Argument::any())->shouldBeCalled()->willThrow(new ModelNotFoundException());
        $this->instance(DataUserRepository::class, $dataUserRepository->reveal());

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $retrievedUser = $provider->retrieveByCredentials(['identifier' => 'abc123']);

        $this->assertNull($retrievedUser);
    }

    /** @test */
    public function it_returns_null_if_the_control_user_is_not_found(){
        $dataUser = factory(DataUser::class)->create();

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['unique_id' => 'abc123'])->shouldBeCalled()->willReturn($dataUser);
        $this->instance(DataUserRepository::class, $dataUserRepository->reveal());

        $controlUserRepository = $this->prophesize(UserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id)->shouldBeCalled()->willThrow(new ModelNotFoundException());
        $this->instance(UserRepository::class, $controlUserRepository->reveal());

        IdentifierAttribute::setValue('unique_id');

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $retrievedUser = $provider->retrieveByCredentials(['identifier' => 'abc123']);

        $this->assertNull($retrievedUser);
    }

    /** @test */
    public function it_returns_null_if_the_authentication_user_is_not_found(){
        $dataUser = factory(DataUser::class)->create();
        $user = factory(User::class)->create(['data_provider_id' => $dataUser]);

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['unique_id' => 'abc123'])->shouldBeCalled()->willReturn($dataUser);
        $this->instance(DataUserRepository::class, $dataUserRepository->reveal());

        $controlUserRepository = $this->prophesize(UserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id)->shouldBeCalled()->willReturn($user);
        $this->instance(UserRepository::class, $controlUserRepository->reveal());

        IdentifierAttribute::setValue('unique_id');

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromControlId($user->id)->shouldBeCalled()->willThrow(new ModelNotFoundException());

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $retrievedUser = $provider->retrieveByCredentials(['identifier' => 'abc123']);

        $this->assertNull($retrievedUser);
    }

    /** @test */
    public function it_returns_the_authentication_user(){
        $dataUser = factory(DataUser::class)->create();
        $user = factory(User::class)->create(['data_provider_id' => $dataUser]);
        $authenticationUser = AuthenticationUser::factory()->create();

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['unique_id' => 'abc123'])->shouldBeCalled()->willReturn($dataUser);
        $this->instance(DataUserRepository::class, $dataUserRepository->reveal());

        $controlUserRepository = $this->prophesize(UserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id)->shouldBeCalled()->willReturn($user);
        $this->instance(UserRepository::class, $controlUserRepository->reveal());

        IdentifierAttribute::setValue('unique_id');

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromControlId($user->id)->shouldBeCalled()->willReturn($authenticationUser);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $retrievedUser = $provider->retrieveByCredentials(['identifier' => 'abc123']);

        $this->assertInstanceOf(AuthenticationUser::class, $retrievedUser);
        $this->assertModelEquals($authenticationUser, $retrievedUser);
    }

    /** @test */
    public function a_different_identifier_works(){
        $dataUser = factory(DataUser::class)->create();
        $user = factory(User::class)->create(['data_provider_id' => $dataUser]);
        $authenticationUser = AuthenticationUser::factory()->create();

        $dataUserRepository = $this->prophesize(DataUserRepository::class);
        $dataUserRepository->getWhere(['unique_id_two' => 'abc123'])->shouldBeCalled()->willReturn($dataUser);
        $this->instance(DataUserRepository::class, $dataUserRepository->reveal());

        $controlUserRepository = $this->prophesize(UserRepository::class);
        $controlUserRepository->getByDataProviderId($dataUser->id)->shouldBeCalled()->willReturn($user);
        $this->instance(UserRepository::class, $controlUserRepository->reveal());

        IdentifierAttribute::setValue('unique_id_two');

        $userRepository = $this->prophesize(AuthenticationUserRepository::class);
        $userRepository->getFromControlId($user->id)->shouldBeCalled()->willReturn($authenticationUser);

        $provider = new AuthenticationUserProvider($userRepository->reveal());
        $retrievedUser = $provider->retrieveByCredentials(['identifier' => 'abc123']);

        $this->assertInstanceOf(AuthenticationUser::class, $retrievedUser);
        $this->assertModelEquals($authenticationUser, $retrievedUser);
    }

}
