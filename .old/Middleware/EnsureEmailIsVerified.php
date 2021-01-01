<?php

namespace App\Http\Middleware;

use BristolSU\Support\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{

    public function handle(Request $request, \Closure $next)
    {
        if($this->mustVerifyEmail($request->user())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::route('verification.notice');
        }

        return $next($request);
    }

    /**
     * Does the user need to verify their email?
     *
     * Will return false (let a user pass) if
     * - There is no user logged in
     * - Verification is turned off
     * - The user is already verified
     *
     * @param User $user
     * @return bool
     */
    private function mustVerifyEmail(?User $user)
    {
        return $user !== null
            && siteSetting('authentication.verification.required', false)
            && ! $user->hasVerifiedEmail();
    }

}
