<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\PasswordHasBeenReset;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\ResetPasswordRequest;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\AuthenticationUserRepository;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Linkeys\UrlSigner\Facade\UrlSigner;

class ResetPasswordController extends Controller
{

    public function showForm(Request $request,
                             AuthenticationUserRepository $userRepository,
                             AuthenticationUserResolver $userResolver)
    {

        $user = $this->getUser($request, $userRepository);
        $userResolver->setUser($user);

        return view('portal-auth::pages.reset_password')->with([
            'email' => $user->controlUser()->data()->email()
        ]);
    }

    public function reset(ResetPasswordRequest $request, AuthenticationUserResolver $userResolver)
    {
        $user = $userResolver->getUser();
        $password = $request->input('password');

        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordHasBeenReset($user));

        return redirect()->intended(
            DefaultHome::getValueAsPath($user->controlId())
        );

    }

    /**
     * Get the user from the request
     *
     * @param Request $request
     * @param AuthenticationUserRepository $userRepository
     * @return AuthenticationUser
     * @throws ModelNotFoundException
     */
    protected function getUser(Request $request, AuthenticationUserRepository $userRepository): AuthenticationUser
    {
        $id = $request->get('user_id');
        return $userRepository->getById($id);
    }

}
