<?php

namespace BristolSU\Auth\Tests;

use BristolSU\Auth\AuthServiceProvider;
use BristolSU\ControlDB\ControlDBServiceProvider;
use BristolSU\Support\Testing\AssertsEloquentModels;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Linkeys\UrlSigner\Providers\UrlSignerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations, AssertsEloquentModels;

    /**
     * Initialise the test case.
     *
     * Loads migrations and factories for the sdk
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
        $this->withFactories(__DIR__.'/../database/factories');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('passport.storage.database.connection', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:UTyp33UhGolgzCK5CJmT+hNHcA+dJyp3+oINtX+VoPI=');
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
            ControlDBServiceProvider::class,
            UrlSignerServiceProvider::class
        ];
    }

}
