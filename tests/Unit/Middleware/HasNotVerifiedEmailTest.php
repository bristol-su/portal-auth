<?php


namespace BristolSU\Auth\Tests\Unit\Middleware;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Middleware\HasNotVerifiedEmail;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HasNotVerifiedEmailTest extends TestCase
{

    /** @test */
    public function it_redirects_a_user_if_their_email_is_verified(){
        Route::name('portal1')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('portal1');

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => Carbon::now()]);

        $userResolver = $this->prophesize(AuthenticationUserResolver::class);
        $userResolver->getUser()->shouldBeCalled()->willReturn($user);

        $request = Request::create('/test');

        $middleware = new HasNotVerifiedEmail($userResolver->reveal());
        $response = $middleware->handle($request, function($request) {
            $this->assertFalse(true, 'The callback should not have been called, but it was.');
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue($response->isRedirect('http://localhost/portal1'));
    }

    /** @test */
    public function it_calls_the_callback_if_their_email_is_not_verified(){

        Route::name('portal1')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('portal1');

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);

        $userResolver = $this->prophesize(AuthenticationUserResolver::class);
        $userResolver->getUser()->shouldBeCalled()->willReturn($user);

        $request = Request::create('/test');

        $middleware = new HasNotVerifiedEmail($userResolver->reveal());
        $this->assertTrue(
            $middleware->handle($request, function($paramRequest) use ($request) {
                $this->assertSame($paramRequest, $request);
                return true;
            })
        );

    }

}
