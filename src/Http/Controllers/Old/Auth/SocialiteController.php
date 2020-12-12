<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use BristolSU\ControlDB\Contracts\Repositories\DataUser;
use BristolSU\Support\User\Contracts\UserAuthentication;
use BristolSU\Support\User\Contracts\UserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application's login form.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        if(in_array($provider, siteSetting('thirdPartyAuthentication.providers', []))) {
            return Socialite::driver($provider)->redirect();
        }
        return redirect()->route('login')
            ->withErrors(['identifier' => 'You cannot log in through ' . $provider]);
    }

    /**
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ValidationException
     */
    public function handleCallback($provider)
    {

        $user = Socialite::driver($provider)->user();

        [ $databaseUser, $newlyRegistered ] = $this->getUserFromSocialUser($user);

        app(UserAuthentication::class)->setUser($databaseUser);

        return ($newlyRegistered?redirect('welcome'):redirect('portal'));

    }

    /**
     * Gets a database user from a socialite user
     *
     * @param \Laravel\Socialite\Contracts\User $user
     * @return array [ $databaseUser, bool $newlyRegistered ]
     * @throws ValidationException
     */
    protected function getUserFromSocialUser(\Laravel\Socialite\Contracts\User $user)
    {

        $dataUser = $this->getDataUser($user);
        $controlUser = $this->getControlUser($dataUser);

        $newlyRegistered = false;
        try {
            $databaseUser = app(UserRepository::class)->getFromControlId($controlUser->id());
        } catch (ModelNotFoundException $e) {
            $databaseUser = app(UserRepository::class)->create(['control_id' => $controlUser->id()]);
            $databaseUser->email_verified_at = Carbon::now();
            $databaseUser->save();
            $newlyRegistered = true;
        }

        return [ $databaseUser, $newlyRegistered ];
    }

    /**
     * Get a data user from a socialite user
     *
     * @param \Laravel\Socialite\Contracts\User $user
     * @return mixed
     * @throws ValidationException
     */
    protected function getDataUser(\Laravel\Socialite\Contracts\User $user)
    {
        if($user->getEmail() !== null) {
            try {
                return app(DataUser::class)->getWhere(['email' => $user->getEmail()]);
            } catch (ModelNotFoundException $e) {
                if (! siteSetting('authentication.authorization.requiredAlreadyInData', false)) {
                    return app(DataUser::class)->create($user->getName(), null, $user->getEmail(), null, $user->getNickname());
                }
            }
        }
        throw ValidationException::withMessages([
            'identifier' => siteSetting('authentication.messages.notInData',
                'We didn\'t recognise your details. Please create an account on our website.')
        ])->redirectTo(app(UrlGenerator::class)->route('login'));
    }

    /**
     * Get a control user from a socialite user
     *
     * @param \BristolSU\ControlDB\Contracts\Models\DataUser $dataUser
     * @return mixed
     * @throws ValidationException
     */
    protected function getControlUser(\BristolSU\ControlDB\Contracts\Models\DataUser $dataUser)
    {
        try {
            return app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->getByDataProviderId($dataUser->id());
        } catch (ModelNotFoundException $e) {
            if (! siteSetting('authentication.authorization.requiredAlreadyInControl', false)) {
                return app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->create($dataUser->id());
            }
        }

        throw ValidationException::withMessages([
            'identifier' => siteSetting('authentication.messages.notInControl',
                'You aren\'t currently registered in our systems. Please contact us.')
        ]);
    }


}
