<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\RegisterRequest;
use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Contracts\Repositories\DataUser as DataUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        return view('portal-auth::pages.register');
    }

    /**
     * Register the user.
     *
     * This registration method is only for standard registration, not single sign on.
     *
     * @param Request $request
     * @param AuthenticationUserResolver $userResolver
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function register(RegisterRequest $request, AuthenticationUserResolver $userResolver)
    {
        $user = $this->registerUser($request);

        $userResolver->setUser($user);

        return redirect(DefaultHome::getValue($user->controlId()));
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

        // Get or create the data user
        $dataUser = $this->getOrCreateDataUser(
            $request->input('identifier')
//            siteSetting('authentication.registration_identifier.identifier', 'email'),
//            ! siteSetting('authentication.authorization.requiredAlreadyInData', false)
        );

        $controlUser = $this->getOrCreateControlUser(
            $dataUser, ! siteSetting('authentication.authorization.requiredAlreadyInControl', false)
        );

        return $this->createUser($controlUser, $request->input('password'));

    }

    /**
     * Retrieve or register a data user
     *
     * @param string $identifier The user identifier
     *
     * @return \BristolSU\ControlDB\Contracts\Models\DataUser
     * @throws ValidationException
     */
    protected function registerDataUser(string $identifier): \BristolSU\ControlDB\Contracts\Models\DataUser
    {
        $parameters = [IdentifierAttribute::getValue() => $identifier];
        try {
            return app(DataUserRepository::class)->getWhere($parameters);
        } catch (ModelNotFoundException $e) {
            if (DataUserRegistrationEnabled::getValue()) {
                return app()->call([DataUserRepository::class, 'create'], $parameters);
            }
        }

        throw ValidationException::withMessages([
            'identifier' => DataUserRegistrationNotAllowedMessage::getValue()
        ]);
    }

    /**
     * @param \BristolSU\ControlDB\Contracts\Models\DataUser $dataUser
     * @param bool $shouldCreate
     * @return \BristolSU\ControlDB\Contracts\Models\User
     * @throws ValidationException
     */
    protected function getOrCreateControlUser(\BristolSU\ControlDB\Contracts\Models\DataUser $dataUser, bool $shouldCreate = true): \BristolSU\ControlDB\Contracts\Models\User
    {
        try {
            return app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->getByDataProviderId($dataUser->id());
        } catch (ModelNotFoundException $e) {
            if ($shouldCreate) {
                return app(\BristolSU\ControlDB\Contracts\Repositories\User::class)->create($dataUser->id());
            }
        }

        throw ValidationException::withMessages([
            'identifier' => siteSetting('authentication.messages.notInControl',
                'You aren\'t currently registered in our systems. Please contact us.')
        ]);
    }

    /**
     * Create a database user
     *
     * @param \BristolSU\ControlDB\Contracts\Models\User $controlUser
     * @param string $password
     * @return User
     * @throws ValidationException
     */
    protected function createUser(\BristolSU\ControlDB\Contracts\Models\User $controlUser, string $password): User
    {
        try {
            app(UserRepository::class)->getFromControlId($controlUser->id());
            throw ValidationException::withMessages([
                'identifier' => siteSetting('authentication.messages.alreadyRegistered',
                    'You have already registered!')
            ]);
        } catch (ModelNotFoundException $e) {
        }

        $user = app(UserRepository::class)->create(['control_id' => $controlUser->id()]);
        $user->password = Hash::make($password);
        $user->save();
        return $user;
    }

}
