<?php


namespace BristolSU\Auth\Tests;


use BristolSU\ControlDB\ControlDBServiceProvider;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\SupportServiceProvider;
use BristolSU\Support\Testing\ActivityInstance\SessionActivityInstanceResolver;
use BristolSU\Support\Testing\Authentication\SessionAuthentication;
use BristolSU\Support\Testing\CreatesSdkEnvironment;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\Dusk\TestCase;

class BrowserTestCase extends TestCase
{
    use DatabaseMigrations;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        static::$baseServeHost = env('APP_URL');
        static::$baseServePort = env('DUSK_BASE_SERVE_PORT');
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Initialise the test case.
     *
     * Loads migrations and factories for the sdk
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__.'/../vendor/bristol-su/support/database/migrations'));
    }

    /**
     * Set up the Orchestra Environment
     *
     * - Set up the memory database connection
     * - Set up the Sdk environment
     *
     * @param Application $app Application to set up
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.url', static::baseServeUrl());
        $app['config']->set('database.default', 'testing');
        $app['config']->set('passport.storage.database.connection', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/Browser/testdb.sqlite',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:UTyp33UhGolgzCK5CJmT+hNHcA+dJyp3+oINtX+VoPI=');

        $app->bind(Authentication::class, SessionAuthentication::class);
        $app->bind(ActivityInstanceResolver::class, SessionActivityInstanceResolver::class);

        \Orchestra\Testbench\Dusk\Options::withoutUI();

    }

    /**
     * Get the service providers the sdk registers and requires.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SupportServiceProvider::class,
            ControlDBServiceProvider::class
        ];
    }

}
