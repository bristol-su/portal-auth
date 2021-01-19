<?php

namespace BristolSU\Auth\Tests\Unit\Social;

use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\Social\SocialUserRepository;
use BristolSU\Auth\Tests\TestCase;
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
        $resolvedSocialUser = $repo->getById(50);
    }

    /** @test */
    public function getByProviderId_tests(){

    }
}
