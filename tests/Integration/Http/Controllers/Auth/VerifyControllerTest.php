<?php

namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;

use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Support\Facades\Event;

class VerifyControllerTest extends TestCase
{

    /** @test */
    public function showVerifyPage_redirects_if_email_verified(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->get('/verify');
        $response->assertViewIs('portal-auth::pages.verify_email');
    }

    /** @test */
    public function showVerifyPage_returns_the_correct_view(){
        $user = AuthenticationUser::factory()->create();
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

        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->from('login-1')->post('/verify/resend');
        $response->assertRedirect('http://localhost/login-1');

        Event::assertDispatched(UserVerificationRequestGenerated::class);
    }

    /** @test */
    public function resend_redirects_if_email_verified(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->get('/verify/resend');
        $response->assertRedirect();

        Event::assertNotDispatched(UserVerificationRequestGenerated::class);
    }

    /** @test */
    public function resend_only_allows_3_resends_a_minute(){
        Event::fake(UserVerificationRequestGenerated::class);

        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->from('login-page-1')
            ->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
        $response->assertRedirect('http://localhost/login-page-1');

        $response = $this->from('login-page-1')
            ->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
        $response->assertRedirect('http://localhost/login-page-1');

        $response = $this->from('login-page-1')
            ->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
        $response->assertRedirect('http://localhost/login-page-1');

        $response = $this->from('login-page-1')
            ->post('/verify/resend', [], ['REMOTE_ADDR' => '192.0.0.44']);
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

        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->from('login-page-1')
            ->get('/verify/authorize?uuid=224324');
        dd($response);
    }

    /** @test */
    public function verify_verifies_the_user_if_the_link_is_correct(){

    }

    /** @test */
    public function verify_can_verify_other_users_on_their_behalf_by_clicking_the_link(){

    }

}
