<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserVerificationRequestGenerated;
use App\Http\Controllers\Controller;
use App\Support\DrawerTag;
use BristolSU\ControlDB\Contracts\Repositories\DataUser;
use BristolSU\Support\User\Contracts\UserAuthentication;
use BristolSU\Support\User\Contracts\UserRepository;
use BristolSU\Support\User\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Handle registration of a user
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * - Apply the guest middleware
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('pages.auth.register')
            ->with('drawerTag', DrawerTag::NONE);

    }

    /**
     * Register the user.
     *
     * This registration method is only for standard registration, not single sign on.
     *
     * @param Request $request
     * @param UserAuthentication $databaseUserAuthentication
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function register(Request $request, UserAuthentication $databaseUserAuthentication)
    {
        $this->validator($request->all())->validate();

        $user = $this->registerUser($request);

        if (siteSetting('authentication.verification.required', false)) {
            event(new UserVerificationRequestGenerated($user));
        } else {
            event(new Registered($user));
        }

        $databaseUserAuthentication->setUser($user);

        return redirect('welcome');
    }

    /**
     * Create a validator for the register request
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return Validator::make($data, [
            'identifier' => siteSetting('authentication.registration_identifier.validation', ''),
            'password' => siteSetting('authentication.password.validation', 'required|confirmed|min:6')
        ]);
    }

    /**
     * Create a user
     *
     * @param Request $request
     * @return User
     * @throws ValidationException
     */
    protected function registerUser(Request $request): User
    {
        // Get or create the data user
        $dataUser = $this->getOrCreateDataUser(
            $request->input('identifier'),
            siteSetting('authentication.registration_identifier.identifier', 'email'),
            ! siteSetting('authentication.authorization.requiredAlreadyInData', false)
        );

        $controlUser = $this->getOrCreateControlUser(
            $dataUser, ! siteSetting('authentication.authorization.requiredAlreadyInControl', false)
        );

        return $this->createUser($controlUser, $request->input('password'));

    }

    /**
     * Get or create a data user
     *
     * @param string $identifierValue The value of the identifier to use
     * @param string $identifier Identifier to create the user with
     * @param bool $shouldCreate
     *
     * @return \BristolSU\ControlDB\Contracts\Models\DataUser
     * @throws ValidationException
     */
    protected function getOrCreateDataUser(string $identifierValue, string $identifier = 'email', bool $shouldCreate = true): \BristolSU\ControlDB\Contracts\Models\DataUser
    {
        try {
            return app(DataUser::class)->getWhere([$identifier => $identifierValue]);
        } catch (ModelNotFoundException $e) {
            if ($shouldCreate) {
                if ($identifier === 'email') {
                    return app(DataUser::class)->create(null, null, $identifierValue);
                }
                $dataUser = app(DataUser::class)->create();
                $dataUser->saveAdditionalAttribute($identifier, $identifierValue);
                return $dataUser;
            }
        }

        throw ValidationException::withMessages([
            'identifier' => siteSetting('authentication.messages.notInData',
                'We didn\'t recognise your details. Please create an account on our website.')
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
