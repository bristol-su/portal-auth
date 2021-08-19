<?php

namespace BristolSU\Auth;

use BristolSU\Auth\Authentication\AuthenticationUserProvider;
use BristolSU\Auth\Authentication\Contracts\AuthenticationUserResolver;
use BristolSU\Auth\Authentication\ControlResolver\Api as ApiControlResolver;
use BristolSU\Auth\Authentication\ControlResolver\Web as WebControlResolver;
use BristolSU\Auth\Authentication\Resolver\Api as UserApiResolver;
use BristolSU\Auth\Authentication\Resolver\Web as UserWebResolver;
use BristolSU\Auth\Events\PasswordHasBeenReset;
use BristolSU\Auth\Events\PasswordResetRequestGenerated;
use BristolSU\Auth\Events\UserVerificationRequestGenerated;
use BristolSU\Auth\Exceptions\Handler;
use BristolSU\Auth\Listeners\SendPasswordHasBeenResetEmail;
use BristolSU\Auth\Listeners\SendResetPasswordEmail;
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
use BristolSU\Auth\Settings\Messaging\LoginHeader;
use BristolSU\Auth\Settings\Messaging\LoginSubtitle;
use BristolSU\Auth\Settings\Messaging\RegisterSubtitle;
use BristolSU\Auth\Settings\Security\PasswordConfirmationTimeout;
use BristolSU\Auth\Settings\Security\SecurityGroup;
use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Social\Driver\DriverStore;
use BristolSU\Auth\Social\Driver\DriverStoreSingleton;
use BristolSU\Auth\Social\Http\Middleware\LoadsSocialite;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientId;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientSecret;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubEnabled;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubGroup;
use BristolSU\Auth\Social\Settings\SocialDriversCategory;
use BristolSU\Auth\Social\Contracts\SocialUserRepository as SocialUserRepositoryContract;
use BristolSU\Auth\Social\SocialUserRepository;
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
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;
use Illuminate\Database\QueryException;
use Illuminate\Encryption\MissingAppKeyException;

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
        $this->app->register(SocialiteServiceProvider::class);
        $this->app->register(PassportServiceProvider::class);

        $this->app->bind(AuthenticationUserRepositoryContract::class, AuthenticationUserRepository::class);
        $this->app->bind(SocialUserRepositoryContract::class, SocialUserRepository::class);
        $this->app->singleton(DriverStore::class, DriverStoreSingleton::class);
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
        Passport::withoutCookieSerialization();

        $this->publishes([
            __DIR__ . '/../public/modules/portal-auth' => public_path('modules/portal-auth')
        ], ['module', 'module-assets', 'assets', 'portal-auth']);

        $this->app['router']->pushMiddlewareToGroup('portal-auth', HasVerifiedEmail::class);
        $this->app['router']->aliasMiddleware('portal-not-verified', HasNotVerifiedEmail::class);
        $this->app['router']->aliasMiddleware('socialite', LoadsSocialite::class);
        $this->app['router']->pushMiddlewareToGroup('web', CreateFreshApiToken::class);
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
            ->registerSetting(new ShouldVerifyEmail());

        $this->registerSettings()
            ->category(new AuthCategory())
            ->group(new SecurityGroup())
            ->registerSetting(new ControlUserRegistrationNotAllowedMessage())
            ->registerSetting(new DataUserRegistrationNotAllowedMessage())
            ->registerSetting(new AlreadyRegisteredMessage())
            ->registerSetting(new LoginSubtitle())
            ->registerSetting(new RegisterSubtitle());

        $this->registerSettings()
            ->category(new SocialDriversCategory())
            ->group(new GithubGroup())
            ->registerSetting(new GithubEnabled())
            ->registerSetting(new GithubClientId())
            ->registerSetting(new GithubClientSecret());

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

        $this->app->extend(\Illuminate\Contracts\Debug\ExceptionHandler::class, function($baseHandler) {
            return new Handler($baseHandler);
        });

        $this->app['auth']->resolveUsersUsing(function() {
            return app()->make(AuthenticationUserResolver::class)->getUser();
        });

        Event::listen(UserVerificationRequestGenerated::class, SendVerificationEmail::class);
        Event::listen(PasswordResetRequestGenerated::class, SendResetPasswordEmail::class);
        Event::listen(PasswordHasBeenReset::class, SendPasswordHasBeenResetEmail::class);

        try {
            $this->app->make(DriverStore::class)->register('github', function() {
                $config = app(Repository::class);
                $config->set('services.github.client_id', GithubClientId::getValue());
                $config->set('services.github.client_secret', GithubClientSecret::getValue());
                $config->set('services.github.redirect', '/login/social/github/callback');
            }, GithubEnabled::getValue());
        } catch (QueryException $e) {
            // Drivers couldn't be loaded as settings table hasn't yet been migrated.
        } catch (MissingAppKeyException $e) {
            // The application key hasn't been generated yet
        }
    }

    /**
     * Load the necessary routes
     */
    protected function loadRoutes()
    {
        Route::middleware(config('portal-auth.middleware.web'))->group(__DIR__ . '/../routes/web.php');
        Route::middleware(config('portal-auth.middleware.api'))->group(__DIR__ . '/../routes/api.php');
        Route::prefix('oauth')->group(__DIR__ . '/../routes/passport.php');
    }

    public function overrideAuthConfig(Repository $config)
    {
        $config->set('auth.defaults.guard', 'web');
        $config->set('auth.defaults.passwords', 'users');

        $config->set('auth.guards.web.driver', 'session');
        $config->set('auth.guards.api.driver', 'passport');
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
