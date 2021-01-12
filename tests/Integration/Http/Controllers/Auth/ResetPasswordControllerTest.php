<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\PasswordHasBeenReset;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Linkeys\UrlSigner\Exceptions\LinkInvalidException;
use Linkeys\UrlSigner\Exceptions\LinkNotFoundException;
use Prophecy\Argument;

class ResetPasswordControllerTest extends TestCase
{

    protected function getUrl($user = null)
    {
        if($user === null) {
            $user = AuthenticationUser::factory()->create();
        }
        return \Linkeys\UrlSigner\Facade\UrlSigner::sign(
            app(UrlGenerator::class)->route('password.reset'),
            ['user_id' => $user->id]
        )->getFullUrl();
    }

    /** @test */
    public function showForm_throws_an_exception_if_the_link_uuid_is_wrong(){
        $response = $this->get('/password/reset?uuid=abc123');

        $exception = $response->exception;

        $this->assertNotNull($exception);
        $this->assertInstanceOf(LinkNotFoundException::class, $exception);
        $this->assertEquals('Invalid Link', $exception->getMessage());
    }

    /** @test */
    public function showForm_redirects_if_a_user_is_logged_in(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->get($this->getUrl($user));

        $response->assertRedirect('http://localhost/portal-new');

    }

    /** @test */
    public function showForm_returns_the_correct_view(){
        $response = $this->get($this->getUrl());

        $response->assertViewIs('portal-auth::pages.reset_password');
    }

    /** @test */
    public function showForm_passes_the_correct_email(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => Carbon::now()]);

        $response = $this->get($this->getUrl($user));
        $response->assertViewHas('email', 'test@example.com');
    }

    /** @test */
    public function showForm_passes_a_reset_link_url_that_allows_resetPassword_to_be_called(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['id' => 1002, 'control_id' => $controlUser->id(), 'email_verified_at' => Carbon::now()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->get($this->getUrl($user));
        $response->assertViewHas('formUrl');

        $data = $response->original->gatherData();
        $formUrl = $data['formUrl'];

        $response2 = $this->post($formUrl, [
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);
        $response2->assertRedirect('http://localhost/portal-new');
        $this->assertAuthenticated('web');
    }

    /** @test */
    public function resetPassword_throws_an_exception_if_the_link_uuid_is_not_valid(){
        $response = $this->post('/password/reset?uuid=abc123');

        $exception = $response->exception;

        $this->assertNotNull($exception);
        $this->assertInstanceOf(LinkNotFoundException::class, $exception);
        $this->assertEquals('Invalid Link', $exception->getMessage());
    }

    /** @test */
    public function resetPassword_returns_an_exception_if_a_user_is_logged_in(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user));

        $response->assertRedirect('http://localhost/portal-new');

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_is_not_given(){
        $response = $this->from('/password/reset')->post($this->getUrl(), [
            'password_confirmation' => 'secret123'
        ]);
        $response->assertRedirect('password/reset');
        $response->assertValidationErrorsEqual([
            'password' => 'The password field is required.'
        ]);
    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_confirmation_is_not_given(){
        $response = $this->from('/password/reset')->post($this->getUrl(), [
            'password' => 'secret123'
        ]);
        $response->assertRedirect('password/reset');
        $response->assertValidationErrorsEqual([
            'password' => 'The password confirmation does not match.'
        ]);
    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_and_password_confirmation_are_not_given(){
        $response = $this->from('/password/reset')->post($this->getUrl());
        $response->assertRedirect('password/reset');
        $response->assertValidationErrorsEqual([
            'password' => 'The password field is required.'
        ]);
    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_and_password_confirmation_are_not_the_same(){
        $response = $this->from('/password/reset')->post($this->getUrl(), [
            'password' => 'secret1234',
            'password_confirmation' => 'secret123'
        ]);
        $response->assertRedirect('password/reset');
        $response->assertValidationErrorsEqual([
            'password' => 'The password confirmation does not match.'
        ]);
    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_is_less_than_6_characters(){
        $response = $this->from('/password/reset')->post($this->getUrl(), [
            'password' => 'secre',
            'password_confirmation' => 'secre'
        ]);
        $response->assertRedirect('password/reset');
        $response->assertValidationErrorsEqual([
            'password' => 'The password must be at least 6 characters.'
        ]);
    }

    /** @test */
    public function resetPassword_passes_validation_if_the_password_is_equal_to_6_characters(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function resetPassword_passes_validation_if_the_password_is_greater_than_to_6_characters(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function resetPassword_is_throttled_to_3_a_minute(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        for($i = 0; $i < 3; $i++) {
            $response = $this->post($this->getUrl($user), [
                'password' => 'secret123',
                'password_confirmation' => 'secret1234'
            ]);
            $response->assertStatus(302);
        }

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret123',
            'password_confirmation' => 'secret1234'
        ]);
        $response->assertStatus(429);
    }

    /** @test */
    public function resetPassword_changes_the_password_of_the_user(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);
        $user->password = Hash::make('original');
        $user->save();

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $this->assertTrue(Hash::check('original', $user->password));

        $response = $this->post($this->getUrl($user), [
            'password' => 'updated',
            'password_confirmation' => 'updated'
        ]);

        $user->refresh();
        $this->assertFalse(Hash::check('original', $user->password));
        $this->assertTrue(Hash::check('updated', $user->password));


    }

    /** @test */
    public function resetPassword_logs_the_user_in(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function resetPassword_logs_the_user_in_using_authentication(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        $userAuthentication = $this->prophesize(AuthenticationUserResolver::class);
        $userAuthentication->hasUser()->willReturn(false);
        $userAuthentication->getUser()->willReturn($user);
        $userAuthentication->setUser(Argument::that(fn($newUser) => $newUser instanceof AuthenticationUser && $newUser->is($user)))
            ->shouldBeCalled();
        $this->instance(AuthenticationUserResolver::class, $userAuthentication->reveal());

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertRedirect();
    }

    /** @test */
    public function resetPassword_fires_a_PasswordHasBeenReset_event(){
        Event::fake(PasswordHasBeenReset::class);

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();

        Event::assertDispatched(
            PasswordHasBeenReset::class,
            fn(PasswordHasBeenReset $event): bool => $event->authenticationUser->is($user)
        );
    }

    /** @test */
    public function resetPassword_returns_to_the_default_home_for_the_user(){
        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->controlId());

        $response = $this->post($this->getUrl($user), [
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('http://localhost/portal-new');
    }





}
