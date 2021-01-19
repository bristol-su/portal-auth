<?php

namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;

use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Testing\Assert as PHPUnit;

class LoginControllerTest extends TestCase
{

    /** @test */
    public function GETlogin_returns_the_correct_view(){
        $response = $this->get('/login');
        $response->assertViewIs('portal-auth::pages.login');
    }

    /** @test */
    public function GETlogin_redirects_if_the_user_is_already_logged_in(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user);

        Route::name('portal')->get('portal', fn() => response('Test', 200));
        DefaultHome::setValue('portal', $user->id);

        $response = $this->get('/login');
        $response->assertRedirect();
    }

    /** @test */
    public function GETlogin_redirects_to_the_setting_value_if_the_user_is_already_logged_in(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->id);

        $response = $this->get('/login');
        $response->assertRedirect();
    }

    /** @test */
    public function GETlogin_passes_enabled_social_drivers_through_to_the_login_view(){
    }

    /** @test */
    public function GETlogin_passes_an_empty_array_to_the_login_view_if_there_are_no_enabled_social_drivers(){
    }

    /** @test */
    public function POSTlogin_fails_validation_if_the_identifier_not_given(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        $response = $this->from('login-page-1')->post('/login', [
            'password' => 'secret123'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'The identifier field is required.'
        ]);
    }

    /** @test */
    public function POSTlogin_fails_validation_if_the_password_not_given(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'password' => 'The password field is required.'
        ]);
    }

    /** @test */
    public function POSTlogin_sends_a_failed_response_if_the_password_is_wrong(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'example@portal.com',
            'password' => 'secret1234',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'These credentials do not match our records.'
        ]);
    }

    /** @test */
    public function POSTlogin_logs_the_user_in_if_password_is_correct(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function POSTlogin_sends_a_redirect_response_to_the_default_home_setting_if_password_is_correct(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $controlUser->id());
        IdentifierAttribute::setValue('email');

        $response = $this->post('/login', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/portal1');
    }

    /** @test */
    public function POSTlogin_sends_a_redirect_response_to_the_intended_route_if_password_is_correct(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        Route::name('abc123-test-2')->get('portal1-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        $response = $this->withSession(['url.intended' => '/portal1-new'])->post('/login', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
        ], ['Referer' => 'http://localhost/login-page-1']);

        $response->assertRedirect('/portal1-new');
    }

    /** @test */
    public function POSTlogin_fails_validation_if_too_many_requests_in_a_given_period(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        for($i=0; $i < 3; $i++) {
            $response = $this->from('login-page-1')->post('/login', [
                'identifier' => 'email@portal.com',
                'password' => 'secret1234'
            ], [
                'REMOTE_ADDR' => '192.0.0.44'
            ]);
            $response->assertRedirect('login-page-1');
            $response->assertValidationErrorsEqual([
                'identifier' => 'These credentials do not match our records.'
            ]);
        }

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'email@portal.com',
            'password' => 'secret123'
        ], [
            'REMOTE_ADDR' => '192.0.0.44'
        ]);

        $response->assertRedirect('login-page-1');
        $response->assertSessionHas('errors');
        $errors = Session::get('errors')->getBag('default');

        $this->assertArrayHasKey('identifier', $errors->toArray());
        $this->assertCount(1, $errors->get('identifier'));
        $this->assertContains($errors->get('identifier')[0], [
            'Too many login attempts. Please try again in 60 seconds.',
            'Too many login attempts. Please try again in 59 seconds.',
            'Too many login attempts. Please try again in 58 seconds.'
        ]);
    }

    /** @test */
    public function POSTlogin_allows_logging_in_after_60_seconds(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');
        IdentifierAttribute::setValue('email');

        for($i=0; $i < 3; $i++) {
            $response = $this->from('login-page-1')->post('/login', [
                'identifier' => 'email@portal.com',
                'password' => 'secret1234'
            ], [
                'REMOTE_ADDR' => '192.0.0.44'
            ]);
            $response->assertRedirect('login-page-1');
            $response->assertValidationErrorsEqual([
                'identifier' => 'These credentials do not match our records.'
            ]);
        }

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'email@portal.com',
            'password' => 'secret123'
        ], [
            'REMOTE_ADDR' => '192.0.0.44'
        ]);
        $response->assertRedirect('login-page-1');
        $response->assertSessionHas('errors');
        $errors = Session::get('errors')->getBag('default');

        $this->assertArrayHasKey('identifier', $errors->toArray());
        $this->assertCount(1, $errors->get('identifier'));
        $this->assertContains($errors->get('identifier')[0], [
            'Too many login attempts. Please try again in 60 seconds.',
            'Too many login attempts. Please try again in 59 seconds.',
            'Too many login attempts. Please try again in 58 seconds.'
        ]);

        Carbon::setTestNow(Carbon::now()->addSeconds(30));

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'email@portal.com',
            'password' => 'secret123'
        ], [
            'REMOTE_ADDR' => '192.0.0.44'
        ]);
        $response->assertRedirect('login-page-1');
        $response->assertSessionHas('errors');
        $errors = Session::get('errors')->getBag('default');

        $this->assertArrayHasKey('identifier', $errors->toArray());
        $this->assertCount(1, $errors->get('identifier'));
        $this->assertContains($errors->get('identifier')[0], [
            'Too many login attempts. Please try again in 30 seconds.',
            'Too many login attempts. Please try again in 29 seconds.',
            'Too many login attempts. Please try again in 28 seconds.'
        ]);

        Carbon::setTestNow(Carbon::now()->addMinute()->addSecond());

        $response = $this->from('login-page-1')->post('/login', [
            'identifier' => 'email@portal.com',
            'password' => 'secret123'
        ], [
            'REMOTE_ADDR' => '192.0.0.44'
        ]);

        $response->assertRedirect('login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'These credentials do not match our records.'
        ]);
    }
}
