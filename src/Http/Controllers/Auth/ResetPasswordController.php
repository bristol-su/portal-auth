<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\DrawerTag;
use BristolSU\Support\User\Contracts\UserRepository;
use BristolSU\Support\User\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/portal';

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
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * @param Request $request
     * @param null $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws AuthorizationException
     */
    public function showResetForm(Request $request, $token = null)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $identifier = $this->getIdentifierFromEmail($request->input('email'));

        return view('pages.auth.passwords.reset')->with(
            ['token' => $token, 'identifier' => $identifier, 'drawerTag' => DrawerTag::NONE]
        );
    }

    /**
     * Get a user identifier from their email address
     *
     * @param string $email
     * @return string Value of their identifier
     * @throws AuthorizationException
     */
    protected function getIdentifierFromEmail(string $email)
    {
        if(siteSetting('authentication.registration_identifier.identifier', 'email') === 'email') {
            return $email;
        } else {
            try {
                $user = app(UserRepository::class)->getWhereEmail($email);
                return $user->controlUser()->data()->getAdditionalAttribute(
                    siteSetting('authentication.registration_identifier.identifier', 'email')
                );
            } catch (ModelNotFoundException $e) {
                throw new AuthorizationException;
            }
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'identifier' => siteSetting('authentication.registration_identifier.validation', ''),
            'password' => siteSetting('authentication.password.validation', 'required|confirmed|min:6')
        ];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'identifier', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('identifier'))
            ->withErrors(['identifier' => trans($response)]);
    }

}
