<?php

namespace BristolSU\Auth\Tests\Integration\Listeners;

use BristolSU\Auth\Notifications\ResetPassword;
use BristolSU\Auth\Notifications\VerifyEmail;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Linkeys\UrlSigner\Facade\UrlSigner;

class SendResetPasswordEmailTest extends TestCase
{

    /** @test */
    public function it_triggers_when_the_verification_event_is_dispatched(){
        Queue::fake();

        $dataUser = factory(DataUser::class)->create(['email' => 'example@portal.com']);
        $controlUser = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $user = AuthenticationUser::factory()->create(['control_id' => $controlUser->id()]);

        Route::name('abc123-test')->get('portal-new', fn() => response('Test', 200));
        DefaultHome::setDefault('abc123-test');

        $response = $this->from('login-page-1')->post('/password/forgot', [
            'identifier' => 'example@portal.com'
        ]);

        Queue::assertPushed(SendQueuedNotifications::class, function($job) {
            $notification = $job->notification;
            $this->assertInstanceOf(ResetPassword::class, $notification);
            return true;
        });
    }

}
