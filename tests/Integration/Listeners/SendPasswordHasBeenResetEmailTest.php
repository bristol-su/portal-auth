<?php

namespace BristolSU\Auth\Tests\Integration\Listeners;

use BristolSU\Auth\Notifications\VerifyEmail;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;

class SendPasswordHasBeenResetEmailTest extends TestCase
{

    /** @test */
    public function it_triggers_when_the_verification_event_is_dispatched(){
        $this->markTestIncomplete('Needs to be changed to test the correct class');
        ShouldVerifyEmail::setValue(false);
        Queue::fake();

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

        Queue::assertPushed(SendQueuedNotifications::class, function($job) {
            $notification = $job->notification;
            $this->assertInstanceOf(VerifyEmail::class, $notification);
            return true;
        });
    }

}
