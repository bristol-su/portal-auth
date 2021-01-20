<?php

namespace BristolSU\Auth\Tests\Integration\Social\Http\Controllers;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Social\Driver\DriverStore;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientId;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientSecret;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubEnabled;
use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Two\User;

class SocialLoginControllerTest extends TestCase
{

    /** @test */
    public function redirect_redirects_to_the_homepage_if_a_user_is_already_logged_in(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user);

        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setValue('test1', $user->id);

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);

        $response = $this->get('/login/social/driver-1');
        $response->assertRedirect('test-123');

    }

    /** @test */
    public function redirect_returns_back_with_a_message_if_the_driver_does_not_exist(){
        Route::name('test1')->get('test-123', fn() => 'test1');

        $response = $this->from('test-123')->get('/login/social/driver-1');
        $response->assertRedirect('test-123');

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'danger',
            'message' => 'You cannot log in using driver-1.'
        ], Session::get('messages')[0]);

    }

    /** @test */
    public function redirect_returns_back_with_a_message_if_the_driver_is_disabled(){
        Route::name('test1')->get('test-123', fn() => 'test1');

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', false);

        $response = $this->from('test-123')->get('/login/social/driver-1');
        $response->assertRedirect('test-123');

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'danger',
            'message' => 'Log in through driver-1 is currently disabled.'
        ], Session::get('messages')[0]);
    }

    /** @test */
    public function redirect_returns_redirect_to_login_with_a_message_if_the_driver_does_not_exist_and_theres_no_page_history(){
        $response = $this->get('/login/social/driver-1');
        $response->assertRedirect('login');

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'danger',
            'message' => 'You cannot log in using driver-1.'
        ], Session::get('messages')[0]);
    }

    /** @test */
    public function redirect_returns_redirect_to_login_with_a_message_if_the_driver_is_disabled_and_theres_no_page_history(){
        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', false);

        $response = $this->get('/login/social/driver-1');
        $response->assertRedirect('login');

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'danger',
            'message' => 'Log in through driver-1 is currently disabled.'
        ], Session::get('messages')[0]);
    }

    /** @test */
    public function redirect_returns_the_socialite_redirect(){
        Route::name('test1')->get('test-123', fn() => 'test1');

        $driver = $this->prophesize(Provider::class);
        $driver->redirect()->shouldBeCalled()->willReturn(redirect('test-123'));
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);

        $response = $this->get('/login/social/driver-1');
        $response->assertRedirect('test-123');
    }

    /** @test */
    public function it_logs_in_the_user_if_a_social_user_already_exists_with_the_same_id_and_redirects(){
        $authenticationUser = AuthenticationUser::factory()->create();
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setValue('test1', $authenticationUser->id);

        $socialUser = SocialUser::factory()->create([
            'provider' => 'driver-1',
            'provider_id' => 'social-id-1',
            'email' => 'test@example.com',
            'name' => 'Toby Twigger',
            'authentication_user_id' => $authenticationUser->id
        ]);
        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');

        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);

        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $this->assertAuthenticatedAs($authenticationUser);
        $this->assertEquals(1, SocialUser::all()->count());
    }

    /** @test */
    public function it_gets_a_dataUser_and_passes_it_params_if_a_social_user_does_not_exist(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');
        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);


        $this->assertDatabaseMissing('control_data_user', [
            'email' => 'test@example.com',
            'first_name' => 'Toby',
            'last_name' => 'Twigger',
            'preferred_name' => 'tobyt'
        ]);

        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $this->assertDatabaseHas('control_data_user', [
            'email' => 'test@example.com',
            'first_name' => 'Toby',
            'last_name' => 'Twigger',
            'preferred_name' => 'tobyt'
        ]);
    }

    /** @test */
    public function it_creates_a_control_user_if_registering(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');
        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);


        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $dataUser = DataUser::where(['email' => 'test@example.com'])->firstOrFail();

        $this->assertDatabaseHas('control_users', [
            'data_provider_id' => $dataUser->id()
        ]);
    }

    /** @test */
    public function it_creates_an_authentication_user_if_registering(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');
        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);


        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $dataUser = DataUser::where(['email' => 'test@example.com'])->firstOrFail();
        $controlUser = \BristolSU\ControlDB\Models\User::where('data_provider_id', $dataUser->id())->firstOrFail();
        $this->assertDatabaseHas('authentication_users', [
            'control_id' => $controlUser->id()
        ]);
    }

    /** @test */
    public function it_creates_a_social_user_if_registering(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');
        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);


        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $this->assertDatabaseHas('social_users', [
            'provider' => 'driver-1',
            'provider_id' => 'social-id-1',
            'email' => 'test@example.com',
            'name' => 'Toby Twigger'
        ]);
        $socialUser = SocialUser::firstOrFail();
        $user = app(AuthenticationUserResolver::class)->getUser();

        $this->assertInstanceOf(AuthenticationUser::class, $user);
        $this->assertInstanceOf(SocialUser::class, $socialUser);

        $this->assertTrue(
            $user->is($socialUser->authenticationUser)
        );
    }

    /** @test */
    public function it_marks_the_user_as_email_verified_if_registering(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');
        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);

        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');

        $user = app(AuthenticationUserResolver::class)->getUser();

        $this->assertTrue(
            $user->hasVerifiedEmail()
        );
    }

    /** @test */
    public function it_logs_in_a_new_user_and_redirects_to_the_default_home(){
        Route::name('test1')->get('test-123', fn() => 'test1');
        DefaultHome::setDefault('test1');

        $socialiteUser = $this->prophesize(User::class);
        $socialiteUser->getId()->willReturn('social-id-1');
        $socialiteUser->getEmail()->willReturn('test@example.com');
        $socialiteUser->getName()->willReturn('Toby Twigger');
        $socialiteUser->getNickname()->willReturn('tobyt');

        $driver = $this->prophesize(Provider::class);
        $driver->user()->shouldBeCalled()->willReturn($socialiteUser->reveal());
        $factory = $this->prophesize(Factory::class);
        $factory->driver('driver-1')->shouldBeCalled()->willReturn($driver->reveal());
        $this->app->instance(Factory::class, $factory->reveal());

        $driverStore = app(DriverStore::class);
        $driverStore->register('driver-1', fn() => 'test', true);

        $response = $this->get('/login/social/driver-1/callback');
        $response->assertRedirect('test-123');
        $this->assertAuthenticated();
    }

}
