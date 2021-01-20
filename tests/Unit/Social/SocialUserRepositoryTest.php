<?php

namespace BristolSU\Auth\Tests\Unit\Social;

use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\Social\SocialUserRepository;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SocialUserRepositoryTest extends TestCase
{

    /** @test */
    public function getById_returns_the_correct_user(){
        $socialUser = SocialUser::factory()->create();

        $repo = new SocialUserRepository();
        $resolvedSocialUser = $repo->getById($socialUser->id());

        $this->assertTrue($socialUser->is($resolvedSocialUser));
    }

    /** @test */
    public function getById_throws_a_ModelNotFoundException_if_the_id_does_not_exist(){
        $this->expectException(ModelNotFoundException::class);

        $repo = new SocialUserRepository();
        $repo->getById(50);
    }

    /** @test */
    public function getByProviderId_returns_a_matching_social_user(){
        $socialUser = SocialUser::factory()->create([
            'provider' => 'github-test',
            'provider_id' => 'the-id-used-by-github-test'
        ]);
        SocialUser::factory()->count(5)->create();

        $repo = new SocialUserRepository();
        $resolvedSocialUser = $repo->getByProviderId('github-test', 'the-id-used-by-github-test');

        $this->assertTrue($socialUser->is($resolvedSocialUser));
    }

    /** @test */
    public function getByProviderId_returns_the_first_social_user_if_there_are_many(){
        $socialUser = SocialUser::factory()->create([
            'provider' => 'github-test',
            'provider_id' => 'the-id-used-by-github-test'
        ]);
        $socialUser2 = SocialUser::factory()->create([
            'provider' => 'github-test',
            'provider_id' => 'the-id-used-by-github-test'
        ]);
        $socialUser3 = SocialUser::factory()->create([
            'provider' => 'github-test',
            'provider_id' => 'the-id-used-by-github-test'
        ]);
        SocialUser::factory()->count(5)->create();

        $repo = new SocialUserRepository();
        $resolvedSocialUser = $repo->getByProviderId('github-test', 'the-id-used-by-github-test');

        $this->assertTrue($socialUser->is($resolvedSocialUser));
    }

    /** @test */
    public function getByProviderId_throws_an_exception_if_there_are_no_matching_providers(){
        $this->expectException(ModelNotFoundException::class);
        $repo = new SocialUserRepository();
        $repo->getByProviderId('giithub-tes', 'the-id-used-by-github-test');

    }

    /** @test */
    public function create_creates_and_returns_the_social_user(){
        AuthenticationUser::factory()->create(['id' => 501]);

        $repo = new SocialUserRepository();
        $socialUser = $repo->create(501, 'github-test', 'test-123-', 'myemail@example.com', 'Toby Tw');

        $this->assertDatabaseHas('social_users', [
            'authentication_user_id' => 501,
            'provider' => 'github-test',
            'provider_id' => 'test-123-',
            'email' => 'myemail@example.com',
            'name' => 'Toby Tw'
        ]);

        $this->assertEquals(501, $socialUser->authenticationUserId());
        $this->assertEquals('github-test', $socialUser->provider());
        $this->assertEquals('test-123-', $socialUser->providerId());
        $this->assertEquals('myemail@example.com', $socialUser->email());
        $this->assertEquals('Toby Tw', $socialUser->name());
    }
}
