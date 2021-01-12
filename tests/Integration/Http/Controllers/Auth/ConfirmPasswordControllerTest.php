<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class ConfirmPasswordControllerTest extends TestCase
{

    /** @test */
    public function showConfirmationPage_returns_the_correct_view(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->get('/password/confirm');
        $response->assertOk();
        $response->assertViewIs('portal-auth::pages.confirm_password');
    }

    /** @test */
    public function showConfirmationPage_redirects_if_a_user_is_not_logged_in(){
        $user = AuthenticationUser::factory()->create();

        $response = $this->get('/password/confirm');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function showConfirmationPage_redirects_if_a_user_has_not_verified_their_email_address(){
        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->get('/password/confirm');
        $response->assertRedirect('http://localhost/verify');
    }

    /** @test */
    public function confirm_redirects_if_a_user_is_not_logged_in(){
        $user = AuthenticationUser::factory()->create();

        $response = $this->get('/password/confirm');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function confirm_redirects_if_a_user_has_not_verified_their_email_address(){
        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->get('/password/confirm');
        $response->assertRedirect('http://localhost/verify');
    }

    /** @test */
    public function confirm_validates_if_the_password_is_not_given(){
        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        $response = $this->post('/password/confirm', []);
        $response->assertStatus(302);
        $response->assertValidationErrorsEqual([
            'password' => 'Please enter your password.'
        ]);
    }

    /** @test */
    public function confirm_validates_if_the_password_is_incorrect(){
        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        $response = $this->post('/password/confirm', [
            'password' => 'secret1234'
        ]);
        $response->assertStatus(302);
        $response->assertValidationErrorsEqual([
            'password' => 'Your password did not match our records.'
        ]);
    }

    /** @test */
    public function confirm_resets_the_confirmation_timeout_if_password_correct(){
        $original = Carbon::now()->subHour();
        $new = Carbon::now();
        Carbon::setTestNow($new);

        Session::put('portal-auth.password_confirmed_at', $original->unix());

        Route::name('abc123-test')->get('test1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        $response = $this->post('/password/confirm', [
            'password' => 'secret123'
        ]);

        $response->assertStatus(302);
        $this->assertEquals($new->unix(), Session::get('portal-auth.password_confirmed_at', null));

    }

    /** @test */
    public function confirm_redirects_to_the_intended_path(){
        Session::put('url.intended', 'http://localhost/test');

        Route::name('abc123-test')->get('test1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        $response = $this->post('/password/confirm', [
            'password' => 'secret123'
        ]);

        $response->assertRedirect('http://localhost/test');
    }

    /** @test */
    public function confirm_redirects_to_home_if_no_intended_path_given(){
        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        Route::name('abc123-test')->get('test1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->post('/password/confirm', [
            'password' => 'secret123'
        ]);

        $response->assertRedirect('http://localhost/test1');
    }

    /** @test */
    public function confirm_is_limited_to_5_attempts_per_minute(){
        $user = AuthenticationUser::factory()->create([
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret123')
        ]);
        $this->be($user, 'web');

        Route::name('abc123-test')->get('test1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        for($i = 0; $i < 5; $i++) {
            $response = $this->post('/password/confirm', [
                'password' => 'secret123'
            ], ['REMOTE_ADDR' => '192.0.0.44']);
            $response->assertRedirect('http://localhost/test1');
        }

        $response = $this->post('/password/confirm', [
            'password' => 'secret123'
        ], ['REMOTE_ADDR' => '192.0.0.44']);
        $response->assertStatus(429);
    }

}
