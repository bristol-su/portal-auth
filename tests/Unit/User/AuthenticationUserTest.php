<?php

namespace BristolSU\Auth\Tests\Unit\User;

use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AuthenticationUserTest extends TestCase
{

    /** @test */
    public function controlId_returns_the_control_id(){
        $user = AuthenticationUser::factory()->create(['id' => 1]);

        $this->assertEquals(1, $user->controlId());
    }

    /** @test */
    public function controlUser_returns_the_control_user(){
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['id' => 1]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $this->assertInstanceOf(\BristolSU\ControlDB\Models\User::class, $user->controlUser());
        $this->assertModelEquals($controlUser, $user->controlUser());
    }

    /** @test */
    public function routeNotificationForMail_returns_the_user_email(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@test.com']);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $this->assertEquals('example@test.com', $user->routeNotificationForMail());
    }

    /** @test */
    public function getEmailForVerification_returns_the_user_email(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@test.com']);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $this->assertEquals('example@test.com', $user->getEmailForVerification());
    }

    /** @test */
    public function getEmailForVerification_throws_a_validation_exception_if_no_email_found(){
        $this->expectException(ValidationException::class);

        $dataUser = factory(DataUser::class)->create(['email' => null]);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $user->getEmailForVerification();
    }

    /** @test */
    public function getEmailForPasswordReset_returns_the_user_email(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@test.com']);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $this->assertEquals('example@test.com', $user->getEmailForPasswordReset());
    }

    /** @test */
    public function getEmailForPasswordReset_throws_a_validation_exception_if_no_email_found(){
        $this->expectException(ValidationException::class);

        $dataUser = factory(DataUser::class)->create(['email' => null]);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $user->getEmailForPasswordReset();
    }

    /** @test */
    public function findForPassport_returns_the_data_user_model_from_an_email_address(){
        $email = 'test@example.com';
        $dataUser = factory(DataUser::class)->create(['email' => $email]);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);
        $dbUser = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $foundUser = (new AuthenticationUser())->findForPassport($email);
        $this->assertInstanceOf(AuthenticationUser::class, $foundUser);
        $this->assertModelEquals($dbUser, $foundUser);
    }

    /** @test */
    public function findForPassport_returns_null_if_the_email_address_does_not_exist(){
        $email = 'test@example.com';

        $foundUser = (new AuthenticationUser())->findForPassport($email);
        $this->assertNull($foundUser);
    }

    /** @test */
    public function findForPassport_returns_null_if_the_control_user_does_not_exist(){
        $email = 'test@example.com';
        $dataUser = factory(DataUser::class)->create(['email' => $email]);

        $foundUser = (new AuthenticationUser())->findForPassport($email);
        $this->assertNull($foundUser);
    }

    /** @test */
    public function findForPassport_returns_null_if_the_database_user_does_not_exist(){
        $email = 'test@example.com';
        $dataUser = factory(DataUser::class)->create(['email' => $email]);
        $controlUser = factory(\BristolSU\ControlDB\Models\User::class)->create(['data_provider_id' => $dataUser->id()]);


        $foundUser = (new AuthenticationUser())->findForPassport($email);
        $this->assertNull($foundUser);
    }

    /** @test */
    public function id_returns_the_id_of_the_model(){
        $user = AuthenticationUser::factory()->create();

        $this->assertEquals($user->id, $user->id());
    }

    /** @test */
    public function social_user_returns_all_social_users_belonging_to_the_user(){
        $user = AuthenticationUser::factory()->create();
        $socialUsers = SocialUser::factory()->count(5)->create([
            'authentication_user_id' => $user->id()
        ]);
        SocialUser::factory()->count(5)->create();

        $resolvedUsers = $user->socialUser;
        $this->assertCount(5, $resolvedUsers);
        foreach($resolvedUsers as $user) {
            $this->assertInstanceOf(SocialUser::class, $user);
        }
    }

    /** @test */
    public function social_user_returns_one_social_user_if_only_one_is_assigned(){
        $user = AuthenticationUser::factory()->create();
        $socialUser = SocialUser::factory()->create([
            'authentication_user_id' => $user->id()
        ]);
        SocialUser::factory()->count(5)->create();

        $resolvedUsers = $user->socialUser;
        $this->assertCount(1, $resolvedUsers);
        $this->assertInstanceOf(SocialUser::class, $resolvedUsers->first());

    }

    /** @test */
    public function social_user_returns_an_empty_collection_if_no_social_users_belong_to_the_user(){
        $user = AuthenticationUser::factory()->create();
        SocialUser::factory()->count(5)->create();

        $resolvedUsers = $user->socialUser;
        $this->assertInstanceOf(Collection::class, $resolvedUsers);
        $this->assertCount(0, $resolvedUsers);
    }

}
