<?php

namespace BristolSU\Auth\Tests;

use BristolSU\Auth\AuthServiceProvider;
use BristolSU\ControlDB\ControlDBServiceProvider;
use BristolSU\Support\SupportServiceProvider;
use BristolSU\Support\Testing\AssertsEloquentModels;
use BristolSU\Support\Testing\HandlesAuthentication;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\MessageBag;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Laracasts\Utilities\JavaScript\JavaScriptServiceProvider;
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

        $app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');

        $this->addTestResponseMacros();
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
            SupportServiceProvider::class,
            ControlDBServiceProvider::class,
            UrlSignerServiceProvider::class,
            JavaScriptServiceProvider::class
        ];
    }

    protected function addTestResponseMacros()
    {
        TestResponse::macro('assertValidationErrorsEqual', function(array $messages, $format = null, $errorBag = 'default') {
            {
                $this->assertSessionHas('errors');

                $messages = (array) $messages;

                $errors = $this->session()->get('errors')->getBag($errorBag);

                foreach ($messages as $key => $value) {
                    if (is_int($key)) {
                        PHPUnit::assertTrue($errors->has($value), "Session missing error: $value");
                    } else {
                        $errorMessages = $errors->get($key, $format);
                        PHPUnit::assertArrayHasKey($key, $errors->toArray(), sprintf('No errors were returned for the %s field', $key));
                        PHPUnit::assertGreaterThan(0, $errorMessages, sprintf('No errors were returned for the %s field', $key));
                        PHPUnit::assertContains(
                            is_bool($value) ? (string) $value : $value, $errorMessages,
                            sprintf(
                                'Failed asserting [%s] is in (%s) ',
                                $value, implode(
                                    ', ',
                                    $errorMessages
                                )
                            )
                        );
                    }
                }

                return $this;
            }
        });
    }

}
