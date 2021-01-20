<?php

namespace BristolSU\Auth\Social\Http\Controllers;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Social\Contracts\SocialUserRepository;
use BristolSU\Auth\Social\Driver\DriverStore;
use BristolSU\Auth\Social\NameParser;
use BristolSU\Auth\Work\GetAuthenticationUserUnit;
use BristolSU\Auth\Work\GetControlUserUnit;
use BristolSU\Auth\Work\GetDataUserUnit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{

    public function redirect(Request $request, string $driver, DriverStore $driverStore)
    {
        if (!$driverStore->hasDriver($driver)) {
            $messages[] = ['type' => 'danger', 'message' => sprintf('You cannot log in using %s.', $driver)];
            session()->flash('messages', $messages);
            return back(302, [], route('login'));
        } elseif (!$driverStore->isEnabled($driver)) {
            $messages[] = ['type' => 'danger', 'message' => sprintf('Log in through %s is currently disabled.', $driver)];
            session()->flash('messages', $messages);
            return back(302, [], route('login'));
        }

        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request,
                             string $driver,
                             SocialUserRepository $socialUserRepository,
                             GetDataUserUnit $getDataUserUnit,
                             GetControlUserUnit $getControlUserUnit,
                             GetAuthenticationUserUnit $getAuthenticationUserUnit,
                             AuthenticationUserResolver $userResolver)
    {
        $socialiteUser = Socialite::driver($driver)->user();

        try {
            $socialUser = $socialUserRepository->getByProviderId($driver, $socialiteUser->getId());
            $authenticationUser = $socialUser->authenticationUser;
        } catch (ModelNotFoundException $e) {
            $dataUser = $getDataUserUnit->do($socialiteUser->getEmail(), 'email', [
                'first_name' => NameParser::parse($socialiteUser->getName())->getFirstName(),
                'last_name' => NameParser::parse($socialiteUser->getName())->getLastName(),
                'preferred_name' => $socialiteUser->getNickname()
            ]);
            $controlUser = $getControlUserUnit->do($dataUser);
            $authenticationUser = $getAuthenticationUserUnit->do($controlUser);
            $socialUser = $socialUserRepository->create($authenticationUser->id(), $driver, $socialiteUser->getId(), $socialiteUser->getEmail(), $socialiteUser->getName());
            $authenticationUser->markEmailAsVerified();
        }

        $userResolver->setUser($authenticationUser);

        return redirect()->route(DefaultHome::getValueAsRouteName($authenticationUser->controlId()));
    }

}
