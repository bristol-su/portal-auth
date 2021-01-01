<?php

namespace BristolSU\Auth\Tests;

use BristolSU\Auth\AuthServiceProvider;
use BristolSU\ControlDB\ControlDBServiceProvider;
use BristolSU\Support\SupportServiceProvider;
use BristolSU\Support\Testing\AssertsEloquentModels;
use BristolSU\Support\Testing\HandlesAuthentication;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Linkeys\UrlSigner\Providers\UrlSignerServiceProvider;
use Prophecy\PhpUnit\ProphecyTrait;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations, AssertsEloquentModels, HandlesAuthentication, ProphecyTrait;

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
            SupportServiceProvider::class,
            ControlDBServiceProvider::class,
            UrlSignerServiceProvider::class
        ];
    }

}
