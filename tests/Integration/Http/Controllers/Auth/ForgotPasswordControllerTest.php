<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\PasswordResetRequestGenerated;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class ForgotPasswordControllerTest extends TestCase
{

    /** @test */
    public function showForm_redirects_to_the_homepage_if_a_user_is_logged_in(){
        $user = AuthenticationUser::factory()->create();
        app(AuthenticationUserResolver::class)->setUser($user);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->get('/password/forgot');
        $response->assertRedirect('http://localhost/portal-new');
    }

    /** @test */
    public function showForm_returns_the_correct_view(){
        $response = $this->get('/password/forgot');
        $response->assertViewIs('portal-auth::pages.forgot_password');
    }

    /** @test */
    public function sendResetLink_redirects_to_the_homepage_if_a_user_is_logged_in(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);
        app(AuthenticationUserResolver::class)->setUser($user);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('http://localhost/portal-new');
    }

    /** @test */
    public function sendResetLink_is_throttled_to_3_a_minute(){
            for($i = 0; $i < 3; $i++) {
            $response = $this->post('/password/forgot', [
                'identifier' => 'example@portal.com'
            ]);
            $response->assertStatus(302);
        }

        $response = $this->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertStatus(429);
    }

    /** @test */
    public function sendResetLink_fails_validation_if_the_identifier_is_not_given(){
        $response = $this->from('password/forgot')->post('/password/forgot');
        $response->assertRedirect('password/forgot');
        $response->assertValidationErrorsEqual([
            'identifier' => 'The identifier field is required.'
        ]);
    }

    /** @test */
    public function sendResetLink_fails_validation_if_the_data_user_with_the_given_attribute_does_not_exist(){
        $response = $this->from('password/forgot')->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('password/forgot');
        $response->assertValidationErrorsEqual([
            'identifier' => 'A user account with the given identifier could not be found'
        ]);
    }

    /** @test */
    public function sendResetLink_fails_validation_if_the_control_user_for_the_data_user_does_not_exist(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);

        $response = $this->from('password/forgot')->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('password/forgot');
        $response->assertValidationErrorsEqual([
            'identifier' => 'A user account with the given identifier could not be found'
        ]);
    }

    /** @test */
    public function sendResetLink_fails_validation_if_the_authentication_user_for_the_control_user_does_not_exist(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);

        $response = $this->from('password/forgot')->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('password/forgot');
        $response->assertValidationErrorsEqual([
            'identifier' => 'A user account with the given identifier could not be found'
        ]);
    }

    /** @test */
    public function sendResetLink_fires_an_event_with_the_correct_user(){
        Event::fake();
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        $response = $this->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('password/forgot');

            Event::assertDispatched(PasswordResetRequestGenerated::class, function(PasswordResetRequestGenerated $event) use ($user) {
            return $user->is($event->authenticationUser);
        });
    }

    /** @test */
    public function sendResetLink_redirects_to_showForm(){
        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'password' => Hash::make('secret123')]);

        $response = $this->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);
        $response->assertRedirect('password/forgot');
    }

    /** @test */
    public function sendResetLink_saves_a_message_to_the_session(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);

        $response = $this->from('login-1')->post('/password/forgot', [
            'identifier' => 'test@example.com'
        ]);

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'success',
            'message' => sprintf('We\'ve sent a password reset email to test@example.com')
        ], Session::get('messages')[0]);

    }



}
