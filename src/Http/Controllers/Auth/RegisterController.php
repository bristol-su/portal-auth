<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\RegisterRequest;
use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\RegistrationEnabled;
use BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Models\DataUser;
use BristolSU\ControlDB\Contracts\Models\User;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\User as UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
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
        $parameters = [IdentifierAttribute::getValue() => $identifier];
        try {
            return app(DataUserRepository::class)->getWhere($parameters);
        } catch (ModelNotFoundException $e) {
            if (DataUserRegistrationEnabled::getValue()) {
                $functionParameters = array_merge([
                    'firstName' => null, 'lastName' => null, 'email' => null, 'dob' => null, 'preferredName' => null
                ], $parameters);
                return app()->call(DataUserRepository::class . '@create', $functionParameters);
            }
        }

        throw ValidationException::withMessages([
            'identifier' => DataUserRegistrationNotAllowedMessage::getValue()
        ]);
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
        try {
            return app(UserRepository::class)->getByDataProviderId($dataUser->id());
        } catch (ModelNotFoundException $e) {
            if (ControlUserRegistrationEnabled::getValue()) {
                return app(UserRepository::class)->create($dataUser->id());
            }
        }

        throw ValidationException::withMessages([
            'identifier' => ControlUserRegistrationNotAllowedMessage::getValue()
        ]);
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
        // Check if the user has already registered
        try {
            $user = app(AuthenticationUserRepository::class)->getFromControlId($controlUser->id());
            throw ValidationException::withMessages([
                'identifier' => AlreadyRegisteredMessage::getValue()
            ]);
        } catch (ModelNotFoundException $e) {
            // The user hasn't been created yet.
        }

        $user = app(AuthenticationUserRepository::class)->create(['control_id' => $controlUser->id()]);
        $user->password = Hash::make($password);
        $user->save();
        return $user;
    }

}
