<?php

namespace BristolSU\Auth\Tests\Unit\Middleware;

use BristolSU\Auth\Middleware\IsGuest;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class IsGuestTest extends TestCase
{

    /** @test */
    public function it_redirects_to_the_default_home_if_a_user_is_logged_in(){
        $request = Request::create('/test');

        Route::name('test-name')->get('test', fn($request) => response('Test', 200));

        $user = $this->newUser();
        $authentication = $this->prophesize(Authentication::class);
        $authentication->getUser()->shouldBeCalled()->willReturn($user);

        $middleware = new IsGuest($authentication->reveal());

        DefaultHome::setValue('test-name', $user->id());

        $response = $middleware->handle($request, function($paramRequest) use ($request) {
            $this->assertTrue(false, 'The callback was called.');
        });

        $this->assertTrue(
            $response->isRedirect(), 'Response status code ['.$response->getStatusCode().'] is not a redirect status code.'
        );

        $this->assertEquals(
            'http://localhost/test', $response->headers->get('Location')
        );

    }

    /** @test */
    public function it_calls_the_callback_if_a_user_is_not_logged_in(){
        $request = Request::create('/test');

        $authentication = $this->prophesize(Authentication::class);
        $authentication->getUser()->shouldBeCalled()->willReturn(null);

        $middleware = new IsGuest($authentication->reveal());

        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );
    }

}
