<?php

namespace BristolSU\Auth\Tests\Unit\Middleware;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Middleware\HasVerifiedEmail;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HasVerifiedEmailTest extends TestCase
{

    /** @test */
    public function it_throws_an_exception_if_a_user_is_not_logged_in(){
        $this->expectException(EmailNotVerified::class);

        $authenticationUserResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authenticationUserResolver->getUser()->shouldBeCalled()->willReturn(null);

        $request = Request::create('/test');

        ShouldVerifyEmail::setValue(true);

        $middleware = new HasVerifiedEmail($authenticationUserResolver->reveal());
        $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });
    }

    /** @test */
    public function it_throws_an_exception_if_the_user_email_verified_at_column_is_null(){
        $this->expectException(EmailNotVerified::class);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $authenticationUserResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authenticationUserResolver->getUser()->shouldBeCalled()->willReturn($user);

        $request = Request::create('/test');

        ShouldVerifyEmail::setValue(true);

        $middleware = new HasVerifiedEmail($authenticationUserResolver->reveal());
        $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });

    }

    /** @test */
    public function it_calls_the_callback_if_the_user_has_verified_their_email(){

        $user = AuthenticationUser::factory()->create(['email_verified_at' => Carbon::now()->subDay()]);
        $authenticationUserResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authenticationUserResolver->getUser()->shouldBeCalled()->willReturn($user);

        $request = Request::create('/test');

        ShouldVerifyEmail::setValue(true);

        $middleware = new HasVerifiedEmail($authenticationUserResolver->reveal());
        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );
    }

    /** @test */
    public function it_calls_the_callback_if_the_user_does_not_need_to_verify_their_email(){

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $authenticationUserResolver = $this->prophesize(AuthenticationUserResolver::class);
        $authenticationUserResolver->getUser()->willReturn($user);

        $request = Request::create('/test');

        ShouldVerifyEmail::setValue(false);

        $middleware = new HasVerifiedEmail($authenticationUserResolver->reveal());
        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );
    }

}
