<?php

namespace BristolSU\Auth\Middleware;

use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use Closure;
use Illuminate\Http\Request;

class HasVerifiedEmail
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
     */
    public function handle($request, Closure $next)
    {
        if ($this->needsEmailVerification() && !$this->emailVerified()) {
            throw new EmailNotVerified();
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

    /**
     * Determine if email verification is required
     *
     * @return bool
     */
    protected function needsEmailVerification(): bool
    {
        return ShouldVerifyEmail::getValue();
    }

}


