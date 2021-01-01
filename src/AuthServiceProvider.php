<?php

namespace BristolSU\Auth;

use BristolSU\Auth\Authentication\AuthenticationUserProvider;
use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Authentication\ControlResolver\Api as ApiControlResolver;
use BristolSU\Auth\Authentication\ControlResolver\Web as WebControlResolver;
use BristolSU\Auth\Authentication\Resolver\Api as UserApiResolver;
use BristolSU\Auth\Authentication\Resolver\Web as UserWebResolver;
use BristolSU\Auth\Middleware\CheckAdditionalCredentialsOwnedByUser;
use BristolSU\Auth\Middleware\HasConfirmedPassword;
use BristolSU\Auth\Middleware\IsAuthenticated;
use BristolSU\Auth\Middleware\IsGuest;
use BristolSU\Auth\Settings\AuthCategory;
use BristolSU\Auth\Settings\Credentials\CredentialsGroup;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Login\DefaultHome;
use BristolSU\Auth\Settings\Login\LoginGroup;
use BristolSU\Auth\Settings\Login\PasswordConfirmationTimeout;
use BristolSU\Auth\User\AuthenticationUserRepository;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository as AuthenticationUserRepositoryContract;
use BristolSU\Support\Authentication\Contracts\Authentication as ControlResolver;
use BristolSU\Support\Settings\Concerns\RegistersSettings;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Database user service provider
 */
class AuthServiceProvider extends ServiceProvider
{
    use RegistersSettings;

    /**
     * Register
     */
    public function register()
    {
        $this->app->bind(AuthenticationUserRepositoryContract::class, AuthenticationUserRepository::class);

        $this->app->call([$this, 'registerAuthenticationResolver']);
        $this->app->call([$this, 'registerControlResolver']);

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
        $this->app['router']->pushMiddlewareToGroup('portal-auth', IsAuthenticated::class);
        $this->app['router']->pushMiddlewareToGroup('portal-auth', CheckAdditionalCredentialsOwnedByUser::class);
        $this->app['router']->pushMiddlewareToGroup('portal-guest', IsGuest::class);
        $this->app['router']->pushMiddlewareToGroup('portal-confirmed', HasConfirmedPassword::class);

        $this->publishes([
            __DIR__.'/../config/portal-auth.php' => config_path('portal-auth.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/portal-auth.php', 'portal-auth'
        );

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new CredentialsGroup())
            ->registerSetting(new IdentifierAttribute());

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new LoginGroup())
            ->registerSetting(new DefaultHome())
            ->registerSetting(new PasswordConfirmationTimeout());

    }


}
