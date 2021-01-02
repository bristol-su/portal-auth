<?php

namespace BristolSU\Auth\Middleware;

use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Support\Authentication\Contracts\Authentication;
use Illuminate\Http\Request;

class IsGuest
{

    /**
     * @var Authentication
     */
    private Authentication $authentication;

    public function __construct(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Check a user is not logged in
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $user = $this->authentication->getUser();
        if($user !== null) {
            return redirect()->route(DefaultHome::getValue($this->authentication->getUser()->id()));
        }
        return $next($request);
    }

}
