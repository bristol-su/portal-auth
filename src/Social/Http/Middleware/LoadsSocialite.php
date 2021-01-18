<?php

namespace BristolSU\Auth\Social\Http\Middleware;

use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientId;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientSecret;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubEnabled;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class LoadsSocialite
{

    protected static array $callbacks = [];

    public function __construct(protected Repository $config)
    {
    }

    public static function loadDriver(string $driver, \Closure $callback)
    {
        static::$callbacks[$driver] = $callback;
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
        foreach(static::$callbacks as $callback) {
            $callback($this->config);
        }

        return $next($request);
    }

}
