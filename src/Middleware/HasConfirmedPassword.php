<?php

namespace BristolSU\Auth\Middleware;

use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Settings\Login\PasswordConfirmationTimeout;
use Closure;
use Illuminate\Http\Request;

class HasConfirmedPassword
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws PasswordUnconfirmed
     */
    public function handle($request, Closure $next)
    {
        if ($this->shouldConfirmPassword($request)) {
            throw new PasswordUnconfirmed();
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param Request $request
     * @return bool
     */
    protected function shouldConfirmPassword(Request $request): bool
    {
        // How many seconds have passed since the password was last confirmed.
        $confirmedAt = time() - $request->session()->get('portal-auth.password_confirmed_at', 0);

        return $confirmedAt > PasswordConfirmationTimeout::getValue();
    }

}


