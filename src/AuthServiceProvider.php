<?php

namespace BristolSU\Auth;

use BristolSU\Auth\Authentication\AuthenticationUserProvider;
use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Authentication\ControlResolver\Api as ApiControlResolver;
use BristolSU\Auth\Authentication\ControlResolver\Web as WebControlResolver;
use BristolSU\Auth\Authentication\Resolver\Api as UserApiResolver;
use BristolSU\Auth\Authentication\Resolver\Web as UserWebResolver;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Exceptions\Handler;
use BristolSU\Auth\Listeners\SendVerificationEmail;
use BristolSU\Auth\Middleware\CheckAdditionalCredentialsOwnedByUser;
use BristolSU\Auth\Middleware\HasConfirmedPassword;
use BristolSU\Auth\Middleware\HasNotVerifiedEmail;
use BristolSU\Auth\Middleware\HasVerifiedEmail;
use BristolSU\Auth\Middleware\IsAuthenticated;
use BristolSU\Auth\Middleware\IsGuest;
use BristolSU\Auth\Middleware\ThrottleRequests;
use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Settings\Access\RegistrationEnabled;
use BristolSU\Auth\Settings\AuthCategory;
use BristolSU\Auth\Settings\Credentials\CredentialsGroup;
use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Settings\Access\AccessGroup;
use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\Settings\Messaging\ControlUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Settings\Security\PasswordConfirmationTimeout;
use BristolSU\Auth\Settings\Security\SecurityGroup;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\Auth\User\AuthenticationUserRepository;
use BristolSU\Auth\User\Contracts\AuthenticationUserRepository as AuthenticationUserRepositoryContract;
use BristolSU\Support\Authentication\Contracts\Authentication as ControlResolver;
use BristolSU\Support\Settings\Concerns\RegistersSettings;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
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
        $this->app['router']->pushMiddlewareToGroup('portal-verified', HasVerifiedEmail::class);
        $this->app['router']->pushMiddlewareToGroup('portal-auth', CheckAdditionalCredentialsOwnedByUser::class);
        $this->app['router']->pushMiddlewareToGroup('portal-guest', IsGuest::class);
        $this->app['router']->pushMiddlewareToGroup('portal-confirmed', HasConfirmedPassword::class);
        $this->app['router']->aliasMiddleware('portal-throttle', ThrottleRequests::class);
        $this->app['router']->aliasMiddleware('portal-not-verified', HasNotVerifiedEmail::class);

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new CredentialsGroup())
            ->registerSetting(new IdentifierAttribute());

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new AccessGroup())
            ->registerSetting(new RegistrationEnabled())
            ->registerSetting(new ControlUserRegistrationEnabled())
            ->registerSetting(new DataUserRegistrationEnabled())
            ->registerSetting(new DefaultHome());

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new SecurityGroup())
            ->registerSetting(new PasswordConfirmationTimeout())
            ->registerSetting(new ShouldVerifyEmail());

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new SecurityGroup())
            ->registerSetting(new ControlUserRegistrationNotAllowedMessage())
            ->registerSetting(new DataUserRegistrationNotAllowedMessage())
            ->registerSetting(new AlreadyRegisteredMessage());

        $this->publishes([
            __DIR__.'/../config/portal-auth.php' => config_path('portal-auth.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../config/portal-auth.php', 'portal-auth'
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'portal-auth');
        $this->loadRoutes();
        $this->app->call([$this, 'overrideAuthConfig']);

        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                return $app->make(AuthenticationUserResolver::class)->getUser();
            });
        });

        $this->app['auth']->resolveUsersUsing(function() {
            return app()->make(AuthenticationUserResolver::class)->getUser();
        });

        Event::listen(UserVerificationRequestGenerated::class, SendVerificationEmail::class);

    }

    /**
     * Load the necessary routes
     */
    protected function loadRoutes()
    {
        Route::middleware(config('portal-auth.middleware.web'))->group(__DIR__ . '/../routes/web.php');
        Route::middleware(config('portal-auth.middleware.api'))->group(__DIR__ . '/../routes/api.php');
    }

    public function overrideAuthConfig(Repository $config)
    {
        $config->set('auth.defaults.guard', 'web');
        $config->set('auth.defaults.passwords', 'users');

        $config->set('auth.guards.web.driver', 'session');
        $config->set('auth.guards.api.driver', 'token');
        $config->set('auth.guards.web.provider', 'database-users');
        $config->set('auth.guards.api.provider', 'database-users');

        $config->set('auth.providers.database-users.driver', 'portal-user-provider');
        Auth::provider('portal-user-provider', function ($app, array $config) {
            return new AuthenticationUserProvider(
                $app->make(AuthenticationUserRepositoryContract::class)
            );
        });
        $config->set('auth.providers.database-users.model', AuthenticationUser::class);

        $config->set('auth.passwords.users.provider', 'database-users');
        $config->set('auth.passwords.users.table', 'password_resets');
        $config->set('auth.passwords.users.expire', 60);

    }


}
