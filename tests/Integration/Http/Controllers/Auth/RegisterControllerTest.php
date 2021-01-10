<?php

namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Access\RegistrationEnabled;
use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

class RegisterControllerTest extends TestCase
{

    /** @test */
    public function GETregister_returns_the_correct_view(){
        ShouldVerifyEmail::setValue(false);
        $response = $this->get('/register');
        $response->assertViewIs('portal-auth::pages.register');
    }

    /** @test */
    public function GETregister_redirects_if_the_user_is_already_logged_in(){
        ShouldVerifyEmail::setValue(false);
        $user = AuthenticationUser::factory()->create();
        $this->be($user);

        Route::name('portal')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('portal', $user->id);

        $response = $this->get('/register');
        $response->assertRedirect();
    }

    /** @test */
    public function GETregister_redirects_to_the_setting_value_if_the_user_is_already_logged_in(){
        ShouldVerifyEmail::setValue(false);
        $user = AuthenticationUser::factory()->create();
        $this->be($user);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->id);

        $response = $this->get('/register');
        $response->assertRedirect('/portal-new');
    }

    /** @test */
    public function GETregister_returns_the_registration_disabled_view_if_registration_disabled(){
        ShouldVerifyEmail::setValue(false);
        RegistrationEnabled::setValue(false);

        $response = $this->get('/register');
        $response->assertViewIs('portal-auth::errors.registration_disabled');
    }

    /** @test */
    public function POSTregister_fails_validation_if_identifier_not_given(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'The identifier field is required.'
        ]);
    }

    /** @test */
    public function POSTregister_fails_validation_if_password_not_given(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password_confirmation' => 'secret123'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'password' => 'The password field is required.'
        ]);
    }

    /** @test */
    public function POSTregister_fails_validation_if_password_confirmation_not_given(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'password' => 'The password confirmation does not match.'
        ]);
    }

    /** @test */
    public function POSTregister_fails_validation_if_password_confirmation_does_not_match_password(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret1234'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'password' => 'The password confirmation does not match.'
        ]);
    }

    /** @test */
    public function POSTregister_redirects_to_the_get_request_if_registration_disabled(){
        ShouldVerifyEmail::setValue(false);
        RegistrationEnabled::setValue(false);

        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/register');
    }

    /** @test */
    public function POSTregister_fails_with_a_custom_error_message_if_data_user_does_not_exist_but_must_do(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        DataUserRegistrationEnabled::setValue(false);
        DataUserRegistrationNotAllowedMessage::setValue('This is a test message setting. Data user registration disabled.');
        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'This is a test message setting. Data user registration disabled.'
        ]);
    }

    /** @test */
    public function POSTregister_fails_with_a_custom_error_message_if_control_user_does_not_exist_but_must_do(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $user = factory(DataUser::class)->create(['email' => 'example@portal.com']);

        ControlUserRegistrationEnabled::setValue(false);
        ControlUserRegistrationNotAllowedMessage::setValue('This is a test message setting. Control user registration disabled.');
        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'This is a test message setting. Control user registration disabled.'
        ]);
    }

    /** @test */
    public function POSTregister_fails_with_a_custom_error_message_if_authentication_user_already_exists(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $control = factory(User::class)->create(['data_provider_id' => $dataUser->id]);
        $user = AuthenticationUser::factory()->create(['control_id' => $control->id()]);

        AlreadyRegisteredMessage::setValue('This is a test message setting. Already registered.');
        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/login-page-1');
        $response->assertValidationErrorsEqual([
            'identifier' => 'This is a test message setting. Already registered.'
        ]);
    }

    /** @test */
    public function POSTregister_creates_a_data_user_if_needed(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');

        $this->assertDatabaseHas('control_data_user', [
            'email' => 'example@portal.com'
        ]);
    }

    /** @test */
    public function POSTregister_creates_a_control_user_if_needed(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');

        $dataUser = DataUser::where(['email' => 'example@portal.com'])->firstOrFail();

        $this->assertDatabaseHas('control_users', [
            'data_provider_id' => $dataUser->id()
        ]);
    }

    /** @test */
    public function POSTregister_creates_an_authentication_user_if_needed(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');

        $dataUser = DataUser::where(['email' => 'example@portal.com'])->firstOrFail();
        $controlUser = User::where(['data_provider_id' => $dataUser->id()])->firstOrFail();

        $this->assertDatabaseHas('authentication_users', [
            'control_id' => $controlUser->id()
        ]);
    }

    /** @test */
    public function POSTregister_logs_in_the_new_user(){
        ShouldVerifyEmail::setValue(false);
        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $this->assertGuest();
        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');
        $this->assertAuthenticated();
    }

    /** @test */
    public function POSTregister_fires_an_event_when_a_new_user_is_created(){
        ShouldVerifyEmail::setValue(false);
        Event::fake(UserVerificationRequestGenerated::class);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');
        $this->assertAuthenticated();

        Event::assertDispatched(UserVerificationRequestGenerated::class);
    }

    /** @test */
    public function POSTregister_redirects_to_the_verification_notice_page_if_verification_required(){
        ShouldVerifyEmail::setValue(true);

        Event::fake(UserVerificationRequestGenerated::class);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/verify');

    }

    /** @test */
    public function POSTregister_redirects_to_the_default_home_page_if_verification_not_required(){
        ShouldVerifyEmail::setValue(false);

        Event::fake(UserVerificationRequestGenerated::class);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/register', [
            'identifier' => 'example@portal.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');

    }

}
