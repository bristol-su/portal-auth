<?php

namespace BristolSU\Auth\Tests\Integration\Middleware;

use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Support\Facades\Route;

class HasVerifiedEmailTest extends TestCase
{

    /** @test */
    public function a_route_redirects_to_the_email_verification_page_if_not_verified(){

        Route::middleware(['portal-auth'])->get('/test123', function() {
            return '';
        });

        $user = AuthenticationUser::factory()->create(['email_verified_at' => null]);
        $this->be($user, 'web');

        $response = $this->get('/test123');
        $response->assertRedirect('http://localhost/verify');
    }

}
