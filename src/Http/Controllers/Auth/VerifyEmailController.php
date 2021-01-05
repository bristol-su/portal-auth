<?php


namespace BristolSU\Auth\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Settings\Access\DefaultHome;

class VerifyEmailController extends Controller
{

    public function showVerifyPage(AuthenticationUserResolver $resolver)
    {
        return $resolver->getUser()->hasVerifiedEmail()
            ? redirect()->intended(DefaultHome::getValue($resolver->getUser()->controlId()))
            : view('portal-auth::pages.verify_email');
    }

    public function verify()
    {
//        $id = $request->get('id');
//        if($id !== $request->user()->id) {
//            throw new AuthorizationException;
//        }
//
//        if ($request->user()->hasVerifiedEmail()) {
//            return redirect($this->redirectPath());
//        }
//
//        if ($request->user()->markEmailAsVerified()) {
//            event(new Verified($request->user()));
//        }
//
//        return redirect($this->redirectPath())->with('verified', true);
    }

    public function resend(AuthenticationUserResolver $resolver)
    {
        if($resolver->getUser()->hasVerifiedEmail()) {
            return redirect()->intended(DefaultHome::getValue($resolver->getUser()->controlId()));
        }

        event(new UserVerificationRequestGenerated($resolver->getUser()));
    }

}
