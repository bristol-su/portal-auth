<?php

namespace BristolSU\Auth\Tests\Unit\Middleware;

use BristolSU\Auth\Middleware\IsAuthenticated;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class IsAuthenticatedTest extends TestCase
{

    /** @test */
    public function it_calls_the_callback_if_a_user_is_logged_in(){
        $request = Request::create('/test');

        $authentication = $this->prophesize(Authentication::class);
        $authentication->hasUser()->shouldBeCalled()->willReturn(true);

        $middleware = new IsAuthenticated($authentication->reveal());

        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );
    }

    /** @test */
    public function it_throws_an_exception_if_a_user_is_not_logged_in(){
        $this->expectException(AuthenticationException::class);

        $request = Request::create('/test');

        $authentication = $this->prophesize(Authentication::class);
        $authentication->hasUser()->shouldBeCalled()->willReturn(false);

        $middleware = new IsAuthenticated($authentication->reveal());

        $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });
    }

}
