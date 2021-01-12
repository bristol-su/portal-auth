<?php

namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class VerifyEmailControllerTest extends TestCase
{

    /** @test */
    public function showVerifyPage_redirects_if_email_verified(){
        $user = AuthenticationUser::factory()->create(['email_verified_at' => Carbon::now()]);
        $this->be($user, 'web');

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setValue('abc123-test', $user->id);

        $response = $this->get('/verify');
        $response->assertRedirect('http://localhost/portal1');
    }

    /** @test */
    public function showVerifyPage_returns_the_correct_view(){
        $user = AuthenticationUser::factory()->create(['id' => 100, 'email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->get('/verify');
        $response->assertViewIs('portal-auth::pages.verify_email');
    }

    /** @test */
    public function showVerifyPage_requires_a_user_to_be_logged_in(){
        $response = $this->get('/verify');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function resend_fires_the_verification_event(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->from('login-1')->post('/verify/resend');
        $response->assertRedirect('http://localhost/verify');

        Event::assertDispatched(UserVerificationRequestGenerated::class);
    }

    /** @test */
    public function resend_flashes_a_message_to_the_session(){

        $dataUser = factory(DataUser::class)->create(['email' => 'test@example.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id(), 'email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->from('login-1')->post('/verify/resend');

        $response->assertSessionHas('messages');
        $this->assertIsArray(Session::get('messages'));
        $this->assertCount(1, Session::get('messages'));
        $this->assertEquals([
            'type' => 'success',
            'message' => sprintf('We\'ve sent another verification email to test@example.com')
        ], Session::get('messages')[0]);

    }

    /** @test */
    public function resend_redirects_if_email_verified(){
        Event::fake(UserVerificationRequestGenerated::class);

        Route::name('portal1')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('portal1');

        $user = AuthenticationUser::factory()->create(['email_verified_at' => Carbon::now()]);
        $this->be($user, 'web');

        $response = $this->post('/verify/resend');
        $response->assertRedirect('/portal1');

        Event::assertNotDispatched(UserVerificationRequestGenerated::class);
    }

    /** @test */
    public function resend_only_allows_3_resends_a_minute(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        for ($i = 0; $i < 3; $i++) {
            $response = $this->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
            $response->assertRedirect('http://localhost/verify');
        }

        $response = $this->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
        $response->assertStatus(429);
    }

    /** @test */
    public function resend_only_allows_a_logged_in_user(){
        $response = $this->post('/verify/resend');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function verify_returns_an_error_if_the_url_is_wrong(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->from('login-page-1')
            ->get('/verify/authorize?uuid=224324');
        $response->assertSessionHas(['messages' => 'This link has expired.']);
    }


    /** @test */
    public function verify_redirects_to_the_resend_page_if_the_url_is_wrong(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->from('login-page-1')
            ->get('/verify/authorize?uuid=224324');
        $response->assertRedirect('/verify');
    }

    /** @test */
    public function verify_verifies_the_user_if_the_link_is_correct(){
        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');
        $this->assertFalse($user->hasVerifiedEmail());

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate('http://localhost/verify/authorize', ['id' => $user->id]);

        $response = $this->get($link->getFullUrl());
        $response->assertRedirect('/portal1');

        $user->fresh();
        $this->assertTrue($user->hasVerifiedEmail());
    }

    /** @test */
    public function verify_redirects_a_user_if_the_email_is_already_verified(){
        $originalVerification = Carbon::now()->subDays(2)->subHours(3);

        $user = AuthenticationUser::factory()->create(['email_verified_at' => $originalVerification]);
        $this->be($user, 'web');

        $this->assertDatabaseHas('authentication_users', [
            'id' => $user->id,
            'email_verified_at' => $originalVerification
        ]);

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate('http://localhost/verify/authorize', ['id' => $user->id]);

        $response = $this->get($link->getFullUrl());
        $response->assertRedirect('portal1');

        $this->assertDatabaseHas('authentication_users', [
            'id' => $user->id,
            'email_verified_at' => $originalVerification
        ]);

    }

    /** @test */
    public function verify_fails_if_the_logged_in_user_is_using_another_users_link(){

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $user2 = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user2, 'web');
        $this->assertFalse($user->hasVerifiedEmail());

        Route::name('abc123-test')->get('portal1', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate('http://localhost/verify/authorize', ['id' => $user->id]);

        $response = $this->get($link->getFullUrl());
        $response->assertStatus(403);

    }


}
