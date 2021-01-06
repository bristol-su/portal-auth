<?php


namespace BristolSU\Auth\Http\Controllers\Auth;


use BristolSU\Auth\Settings\Access\DefaultHome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConfirmPasswordController
{

    public function showConfirmationPage()
    {
        return view('portal-auth::pages.confirm_password');
    }

    public function confirm(Request $request)
    {
        $request->validate(
            ['password' => 'required|password'],
            [
                'password.required' => 'Please enter your password.',
                'password.password' => 'Your password did not match our records.'
            ]
        );

        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended(
            DefaultHome::getValueAsPath()
        );
    }

    /**
     * Reset the password confirmation timeout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function resetPasswordConfirmationTimeout(Request $request)
    {
        $request->session()->put('auth.password_confirmed_at', Carbon::now()->unix());
    }



}
