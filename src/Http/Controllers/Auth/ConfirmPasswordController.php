<?php


namespace BristolSU\Auth\Http\Controllers\Auth;


use BristolSU\Auth\Http\Requests\Auth\ConfirmPasswordRequest;
use BristolSU\Auth\Settings\Access\DefaultHome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConfirmPasswordController
{

    public function showConfirmationPage()
    {
        return view('portal-auth::pages.confirm_password');
    }

    public function confirm(ConfirmPasswordRequest $request)
    {
        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended(
            DefaultHome::getValueAsPath($request->user()->controlId())
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
        $request->session()->put('portal-auth.password_confirmed_at', Carbon::now()->unix());
    }



}
