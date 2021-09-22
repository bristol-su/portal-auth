<?php

namespace BristolSU\Auth\Http\Controllers\Auth;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Events\PasswordHasBeenReset;
use BristolSU\Auth\Http\Controllers\Controller;
use BristolSU\Auth\Http\Requests\Auth\ResetPasswordRequest;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\AuthenticationUserRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Linkeys\UrlSigner\Facade\UrlSigner;

class ResetPasswordController extends Controller
{

    /**
     * @var AuthenticationUserRepository
     */
    private AuthenticationUserRepository $userRepository;

    public function __construct(AuthenticationUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function showForm(Request $request)
    {
        $user = $this->getUser($request);

        return view('portal-auth::pages.reset_password')->with([
            'email' => $user->controlUser()->data()->email(),
            'formUrl' => $this->generateResetFormUrl($user)
        ]);
    }

    public function reset(ResetPasswordRequest $request, AuthenticationUserResolver $userResolver)
    {
        $user = $this->getUser($request);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        $userResolver->setUser($user);

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
    protected function getUser(Request $request): AuthenticationUser
    {
        $id = $request->get('user_id');
        return $this->userRepository->getById($id);
    }

    /**
     * Generate a url to use for the reset function
     *
     * @param AuthenticationUser $user
     * @return mixed
     */
    protected function generateResetFormUrl(AuthenticationUser $user)
    {
        return UrlSigner::sign(
            app(UrlGenerator::class)->route('password.reset.action'),
            ['user_id' => $user->id()],
            '+30 minutes'
        )->getFullUrl();
    }

}
