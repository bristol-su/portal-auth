<?php


namespace BristolSU\Auth\Http\Controllers\Auth;


use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{

    /**
     * @param Request $request
     * @param AuthenticationUserResolver $userResolver
     * @return RedirectResponse
     */
    public function logout(Request $request, AuthenticationUserResolver $userResolver)
    {
        $userResolver->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
