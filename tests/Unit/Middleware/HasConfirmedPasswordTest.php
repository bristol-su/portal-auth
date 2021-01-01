<?php

namespace BristolSU\Auth\Tests\Unit\Middleware;

use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Middleware\HasConfirmedPassword;
use BristolSU\Auth\Settings\Login\PasswordConfirmationTimeout;
use BristolSU\Auth\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HasConfirmedPasswordTest extends TestCase
{

    /** @test */
    public function it_throws_an_exception_if_no_password_confirmation_has_been_done(){
        $this->expectException(PasswordUnconfirmed::class);

        $session = $this->prophesize(SessionInterface::class);

        $request = Request::create('/test');
        $request->setSession($session->reveal());

        $middleware = new HasConfirmedPassword();
        $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });
    }

    /** @test */
    public function it_throws_an_exception_if_the_password_confirmation_is_greater_than_the_password_timeout(){
        $this->expectException(PasswordUnconfirmed::class);

        PasswordConfirmationTimeout::setValue(1800);

        $session = $this->prophesize(SessionInterface::class);
        $session->get('portal-auth.password_confirmed_at', Argument::type('integer'))->shouldBeCalled()->willReturn(Carbon::now()->subSeconds(7200)->unix());

        $request = Request::create('/test');
        $request->setSession($session->reveal());

        $middleware = new HasConfirmedPassword();
        $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });

    }

    /** @test */
    public function it_calls_the_callback_if_the_password_confirmation_is_in_date_and_valid(){
        PasswordConfirmationTimeout::setValue(1800);

        $session = $this->prophesize(SessionInterface::class);
        $session->get('portal-auth.password_confirmed_at', Argument::type('integer'))->shouldBeCalled()->willReturn(Carbon::now()->subSeconds(900)->unix());

        $request = Request::create('/test');
        $request->setSession($session->reveal());


        $middleware = new HasConfirmedPassword();
        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );
    }

}
