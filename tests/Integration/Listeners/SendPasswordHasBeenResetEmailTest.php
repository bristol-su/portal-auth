<?php

namespace BristolSU\Auth\Tests\Integration\Listeners;

use BristolSU\Auth\Notifications\PasswordHasBeenReset;
use BristolSU\Auth\Notifications\VerifyEmail;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Linkeys\UrlSigner\Facade\UrlSigner;

class SendPasswordHasBeenResetEmailTest extends TestCase
{

    /** @test */
    public function it_triggers_when_the_password_has_been_reset_event_is_dispatched(){
        Queue::fake();

        $user = AuthenticationUser::factory()->create();

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $url = UrlSigner::sign(
            app(UrlGenerator::class)->route('password.reset.action'),
            ['user_id' => $user->id()],
            '+30 minutes'
        )->getFullUrl();

        $response = $this->from('login-page-1')->post($url, [
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);

        Queue::assertPushed(SendQueuedNotifications::class, function($job) {
            $notification = $job->notification;
            $this->assertInstanceOf(PasswordHasBeenReset::class, $notification);
            return true;
        });
    }

}
