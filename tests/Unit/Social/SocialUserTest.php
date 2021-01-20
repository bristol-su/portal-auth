<?php

namespace BristolSU\Auth\Tests\Unit\Social;

use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;

class SocialUserTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $socialUser = new SocialUser();
        $this->assertInstanceOf(SocialUser::class, $socialUser);
    }

    /** @test */
    public function it_can_be_created_with_a_factory(){
        $socialUser = SocialUser::factory()->create();
        $this->assertInstanceOf(SocialUser::class, $socialUser);
    }

    /** @test */
    public function the_email_can_be_set_and_retrieved(){
        $socialUser = SocialUser::factory()->create(['email' => 'example@example.com']);
        $this->assertEquals('example@example.com', $socialUser->email());
    }

    /** @test */
    public function the_provider_id_can_be_set_and_retrieved(){
        $socialUser = SocialUser::factory()->create(['provider_id' => 'abc123']);
        $this->assertEquals('abc123', $socialUser->providerId());
    }

    /** @test */
    public function the_provider_can_be_set_and_retrieved(){
        $socialUser = SocialUser::factory()->create(['provider' => 'facebook']);
        $this->assertEquals('facebook', $socialUser->provider());
    }

    /** @test */
    public function the_provider_id_can_be_an_integer(){
        $socialUser = SocialUser::factory()->create(['provider_id' => 1234]);
        $this->assertEquals('1234', $socialUser->providerId());
    }

    /** @test */
    public function the_id_can_be_retrieved_and_increments_itself(){
        $socialUser1 = SocialUser::factory()->create();
        $socialUser2 = SocialUser::factory()->create();
        $this->assertEquals(1, $socialUser1->id());
        $this->assertEquals(2, $socialUser2->id());
    }

    /** @test */
    public function the_authentication_user_id_can_be_set_and_retrieved(){
        $user = AuthenticationUser::factory()->create();
        $socialUser = SocialUser::factory()->create(['authentication_user_id' => $user->id()]);
        $this->assertEquals($user->id(), $socialUser->authenticationUserId());
    }

    /** @test */
    public function the_authentication_user_model_can_be_retrieved(){
        $user = AuthenticationUser::factory()->create();
        $socialUser = SocialUser::factory()->create(['authentication_user_id' => $user->id()]);
        $this->assertTrue($user->is($socialUser->authenticationUser));
    }

    /** @test */
    public function the_name_can_be_set_and_retrieved(){
        $socialUser = SocialUser::factory()->create(['name' => 'Toby Twigger']);
        $this->assertEquals('Toby Twigger', $socialUser->name());
    }

}
