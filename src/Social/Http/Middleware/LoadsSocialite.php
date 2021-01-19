<?php

namespace BristolSU\Auth\Social\Http\Middleware;

use BristolSU\Auth\Social\Driver\DriverLoader;
use Illuminate\Http\Request;

class LoadsSocialite
{

    public function __construct(protected DriverLoader $loader)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->loader->loadAllEnabled();
        return $next($request);
    }

}
