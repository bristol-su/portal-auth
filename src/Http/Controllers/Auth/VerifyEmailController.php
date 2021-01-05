<?php


namespace BristolSU\Auth\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\UnauthenticatedVerificationAllowed;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{

    public function showVerifyPage(AuthenticationUserResolver $resolver)
    {
        return view('portal-auth::pages.verify_email');
    }

    public function verify(Request $request, AuthenticationUserResolver $resolver, AuthenticationUserRepository $userRepository)
    {
        if($request->get('id') !== $resolver->getUser()->id) {
            throw new AuthorizationException();
        }

        $user = $resolver->getUser();

        $user->markEmailAsVerified();

        return redirect()->intended(DefaultHome::getValueAsPath($user->controlId()));
    }

    public function resend(AuthenticationUserResolver $resolver)
    {
        event(new UserVerificationRequestGenerated($resolver->getUser()));

        return redirect()->route('verify.warning');
    }

}
