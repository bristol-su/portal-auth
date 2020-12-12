<?php

namespace BristolSU\Auth;

use BristolSU\Auth\Authentication\AuthenticationUserProvider;
use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Authentication\ControlResolver\Api as ApiControlResolver;
use BristolSU\Auth\Authentication\ControlResolver\Web as WebControlResolver;
use BristolSU\Auth\Authentication\Resolver\Api as UserApiResolver;
use BristolSU\Auth\Authentication\Resolver\Web as UserWebResolver;
use BristolSU\Auth\Helpers\AuthQuery\Generator;
use BristolSU\Auth\Helpers\ResourceIdGenerator\AuthenticationResourceIdGenerator;
use BristolSU\Auth\Helpers\ResourceIdGenerator\ResourceIdGenerator;
use BristolSU\Auth\Middleware\CheckAdditionalCredentialsOwnedByUser;
use BristolSU\Auth\User\AuthenticationUserRepository;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository as AuthenticationUserRepositoryContract;
use BristolSU\Support\Authentication\Contracts\Authentication as ControlResolver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Database user service provider
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     * Register
     */
    public function register()
    {
        $this->app->register(PassportServiceProvider::class);

        $this->app->bind(AuthenticationUserRepositoryContract::class, AuthenticationUserRepository::class);

        $this->app->call([$this, 'registerAuthenticationResolver']);
        $this->app->call([$this, 'registerControlResolver']);


        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                return $app->make(ControlResolver::class)->getUser();
            });
        });

        Auth::provider('portal-user-provider', function(Container $app, array $config) {
            return $app->make(AuthenticationUserProvider::class);
        });
    }

    /**
     * Register the authentication provider.
     *
     * Will register the API authentication provider if '/api/' is in the route, or the web authentication otherwise
     * @param Request $request
     */
    public function registerAuthenticationResolver(Request $request)
    {
        $this->app->bind(AuthenticationUserResolver::class, function($app) use ($request) {
            return ($request->is('api/*') ?
                $app->make(UserApiResolver::class) : $app->make(UserWebResolver::class));
        });
    }

    /**
     * Register the authentication provider.
     *
     * Will register the API authentication provider if '/api/' is in the route, or the web authentication otherwise
     * @param Request $request
     */
    public function registerControlResolver(Request $request)
    {
        $this->app->bind(ControlResolver::class, function($app) use ($request) {
            return ($request->is('api/*') ?
                $app->make(ApiControlResolver::class) : $app->make(WebControlResolver::class));
        });
    }

    /**
     * Boot
     *
     * - Tell Laravel to resolve users from the authentication contract.
     */
    public function boot()
    {
        $this->app['auth']->resolveUsersUsing(function() {
            return app()->make(ControlResolver::class)->getUser();
        });

        $this->app['router']->pushMiddlewareToGroup('auth', CheckAdditionalCredentialsOwnedByUser::class);
        // TODO Set up portal-auth, portal-guest and portal-confirmed groups.


    }


}
