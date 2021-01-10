<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\User\AuthenticationUserRepository;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Linkeys\UrlSigner\Facade\UrlSigner;

class ResetPasswordController extends Controller
{

    public function showForm(Request $request, AuthenticationUserRepository $userRepository)
    {
        $id = $request->get('user_id');
        $user = $userRepository->getById($id);

        return view('portal-auth::pages.reset_password')->with([
            'user' => $id,
            'email' => $user->controlUser()->data()->email(),
            'formUrl' => UrlSigner::sign(
                app(UrlGenerator::class)->route('password.reset.action'),
                ['user_id' => $id],
                '+15 minutes',
                1
            )->getFullUrl()
        ]);
    }

    public function resetPassword()
    {
        try {
            $dataUser = $dataUserRepository->getWhere([IdentifierAttribute::getValue() => $request->input('identifier')]);
            $controlUser = $controlUserRepository->getByDataProviderId($dataUser->id());
            $user = $authenticationUserRepository->getFromControlId($controlUser->id());
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'identifier' => 'A user account with the given email address could not be found'
            ]);
        }

        event(new PasswordResetRequestGenerated($user));

        return redirect()->route('password.forgot');

    }

}
