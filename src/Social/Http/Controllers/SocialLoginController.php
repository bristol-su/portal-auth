<?php

namespace BristolSU\Auth\Social\Http\Controllers;

use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Social\Contracts\SocialUserRepository;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{

    public function redirect(Request $request, string $driver)
    {
        // TODO Check driver is enabled

        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request, string $driver, SocialUserRepository $socialUserRepository)
    {
        $socialUser = Socialite::driver($driver)->user();

        // Try and get a provider with the right provider_id and provider with repository (getUserThroughProviderId)
            // If a user cannot be found
                // GetDataUserUnit, with email as the identifier and pass in as extra params: name, nickname (preferred name)
                // - We will now have a data user
                // GetControlUserUnit with data user
                // GetAuthenticationUserUnit with control user
                // RegisterSocialUserUnit with authentication user, driver and IDs (create).
                // Verify authentication user email address
            // If a user is found, get the authentication user
        // Now have authentication user.
        // $userResolver->setUser($user);
        //         return redirect()->route(DefaultHome::getValueAsRouteName($user->controlId()));

        dd($socialUser);

    }

}
