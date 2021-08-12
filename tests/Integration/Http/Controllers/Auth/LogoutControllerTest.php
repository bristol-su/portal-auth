<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Support\Facades\Session;

class LogoutControllerTest extends TestCase
{

    /** @test */
    public function it_redirects_an_unauthenticated_user(){
        $userResolver = $this->prophesize(AuthenticationUserResolver::class);
        $userResolver->hasUser()->willReturn(false);
        $userResolver->logout()->shouldNotBeCalled();
        $this->app->instance(AuthenticationUserResolver::class, $userResolver->reveal());

        $response = $this->post('/logout');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function it_logs_a_user_out(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    /** @test */
    public function it_redirects_the_user(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $response = $this->post('/logout');
        $response->assertRedirect('http://localhost/login');
    }

    /** @test */
    public function it_logs_out_an_unauthenticated_user(){
        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
    }
}
