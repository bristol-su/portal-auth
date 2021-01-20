<?php

namespace BristolSU\Auth\Tests\Integration\Social\Driver;

use BristolSU\Auth\Social\Driver\DriverLoader;
use BristolSU\Auth\Social\Driver\DriverStoreSingleton;
use BristolSU\Auth\Tests\TestCase;

class DriverLoaderTest extends TestCase
{


    /** @test */
    public function load_throws_an_exception_if_the_driver_not_found(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The test-driver social driver has not been registered.');

        $store = new DriverStoreSingleton();

        $loader = new DriverLoader($store);
        $loader->load('test-driver');

    }

    /** @test */
    public function load_calls_the_driver_callback(){
        $hasBeenCalled = false;

        $store = new DriverStoreSingleton();
        $store->register('test-driver', function() use (&$hasBeenCalled) {
            $hasBeenCalled = true;
        }, true);

        $loader = new DriverLoader($store);
        $loader->load('test-driver');

        $this->assertTrue($hasBeenCalled);
    }

    /** @test */
    public function loadAllEnabled_calls_the_callback_for_all_enabled_drivers(){
        $hasBeenCalled1 = false;
        $hasBeenCalled2 = false;
        $hasBeenCalled3 = false;

        $store = new DriverStoreSingleton();

        $store->register('test-driver-1', function() use (&$hasBeenCalled1) {
            $hasBeenCalled1 = true;
        }, true);
        $store->register('test-driver-2', function() use (&$hasBeenCalled2) {
            $hasBeenCalled2 = true;
        }, false);
        $store->register('test-driver-3', function() use (&$hasBeenCalled3) {
            $hasBeenCalled3 = true;
        }, true);

        $loader = new DriverLoader($store);
        $loader->loadAllEnabled();

        $this->assertTrue($hasBeenCalled1);
        $this->assertFalse($hasBeenCalled2);
        $this->assertTrue($hasBeenCalled3);
    }
}
