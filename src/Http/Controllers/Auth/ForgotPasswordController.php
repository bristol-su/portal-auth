<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\PasswordResetRequestGenerated;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\ForgotPasswordRequest;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use BristolSU\ControlDB\Contracts\Repositories\DataUser;
use BristolSU\ControlDB\Contracts\Repositories\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{

    public function showForm()
    {
        return view('portal-auth::pages.forgot_password');
    }

    public function sendResetLink(ForgotPasswordRequest $request,
                                  DataUser $dataUserRepository,
                                  User $controlUserRepository,
                                  AuthenticationUserRepository $authenticationUserRepository)
    {
        try {
            $dataUser = $dataUserRepository->getWhere([IdentifierAttribute::getValue() => $request->input('identifier')]);
            $controlUser = $controlUserRepository->getByDataProviderId($dataUser->id());
            $user = $authenticationUserRepository->getFromControlId($controlUser->id());
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'identifier' => 'A user account with the given identifier could not be found'
            ]);
        }

        event(new PasswordResetRequestGenerated($user));

        $messages = session()->get('messages', []);
        $messages[] = ['type' => 'success', 'message' => 'We\'ve sent a password reset email to test@example.com'];
        session()->flash('messages', $messages);

        return redirect()->route('password.forgot');
    }

}
