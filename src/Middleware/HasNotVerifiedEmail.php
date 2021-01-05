<?php

namespace BristolSU\Auth\Middleware;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use Closure;
use Illuminate\Http\Request;

class HasNotVerifiedEmail
{

    /**
     * @var AuthenticationUserResolver
     */
    private AuthenticationUserResolver $userResolver;

    public function __construct(AuthenticationUserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

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
        if ($this->emailVerified()) {
            return redirect()->intended(DefaultHome::getValueAsPath($this->userResolver->getUser()->controlId()));
        }

        return $next($request);
    }

    /**
     * Determine if the user has verified their emaiil.
     *
     * @return bool
     */
    protected function emailVerified(): bool
    {
        $user = $this->userResolver->getUser();
        return $user !== null && $user->hasVerifiedEmail();
    }

}


