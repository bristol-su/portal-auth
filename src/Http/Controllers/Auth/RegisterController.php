<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\RegisterRequest;
use BristolSU\Auth\Settings\Access\RegistrationEnabled;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\Work\GetAuthenticationUserUnit;
use BristolSU\Auth\Work\GetControlUserUnit;
use BristolSU\Auth\Work\GetDataUserUnit;
use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Handle registration of a user
 */
class RegisterController extends Controller
{

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        if(RegistrationEnabled::getValue()) {
            return view('portal-auth::pages.register');
        }
        return view('portal-auth::errors.registration_disabled');
    }

    /**
     * Register the user.
     *
     * This registration method is only for standard registration, not single sign on.
     *
     * @param Request $request
     * @param AuthenticationUserResolver $userResolver
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function register(RegisterRequest $request, AuthenticationUserResolver $userResolver)
    {
        if(!RegistrationEnabled::getValue()) {
            return redirect()->route('register');
        }

        $user = $this->registerUser($request);

        $userResolver->setUser($user);

        if(!$user->hasVerifiedEmail()) {
            event(new UserVerificationRequestGenerated($user));

            if(ShouldVerifyEmail::getValue()) {
                return redirect()->route('verify.notice');
            }
        }

        return redirect()->route(DefaultHome::getValueAsRouteName($user->controlId()));
    }

    /**
     * Create a user
     *
     * @param Request $request
     * @return AuthenticationUser
     * @throws ValidationException
     */
    protected function registerUser(Request $request): AuthenticationUser
    {
        // Get the data user, or create one if it doesn't exist.
        $dataUser = $this->registerDataUser($request->input('identifier'));
        // Get the control user, or create one if it doesn't exist
        $controlUser = $this->registerControlUser($dataUser);

        // Get & return the AuthenticationUser, or create one if it doesn't exist
        return $this->registerAuthenticationUser($controlUser, $request->input('password'));

    }

    /**
     * Retrieve or register a data user
     *
     * @param string $identifier The user identifier
     *
     * @return DataUser
     * @throws ValidationException
     */
    protected function registerDataUser(string $identifier): DataUser
    {
        return app(GetDataUserUnit::class)->do($identifier);
    }

    /**
     * Retrieve or register a control user
     *
     * @param DataUser $dataUser
     * @return User
     * @throws ValidationException
     */
    protected function registerControlUser(DataUser $dataUser): User
    {
        return app(GetControlUserUnit::class)->do($dataUser);
    }

    /**
     * Create a database user
     *
     * @param User $controlUser
     * @param string $password
     * @return AuthenticationUser
     * @throws ValidationException
     */
    protected function registerAuthenticationUser(User $controlUser, string $password): AuthenticationUser
    {
        $authenticationUser = app(GetAuthenticationUserUnit::class)->do($controlUser);
        $authenticationUser->password = Hash::make($password);
        $authenticationUser->save();
        return $authenticationUser;
    }

}
