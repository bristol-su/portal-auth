<?php

namespace BristolSU\Auth\Tests\Unit\Social\Driver;

use BristolSU\Auth\Social\Driver\DriverStoreSingleton;
use BristolSU\Auth\Tests\TestCase;

class DriverStoreSingletonTest extends TestCase
{

    /** @test */
    public function getSetup_returns_a_registered_callback_by_driver_key(){
        $store = new DriverStoreSingleton();
        $callback = fn() => 'this is a test';

        $store->register('test-driver-1', $callback, true);
        $this->assertSame($callback, $store->getSetup('test-driver-1'));
    }

    /** @test */
    public function getSetup_throws_an_exception_if_the_driver_has_not_been_registered(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The test-driver-1 social driver has not been registered.');
        $store = new DriverStoreSingleton();

        $store->getSetup('test-driver-1');
    }

    /** @test */
    public function isEnabled_returns_the_driver_status(){
        $store = new DriverStoreSingleton();

        $store->register('test-driver-1', fn() => 'this is a test', true);
        $store->register('test-driver-2', fn() => 'this is a test', false);

        $this->assertTrue($store->isEnabled('test-driver-1'));
        $this->assertFalse($store->isEnabled('test-driver-2'));
    }

    /** @test */
    public function isEnabled_throws_an_exception_if_the_driver_has_not_been_registered(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The test-driver-1 social driver has not been registered.');

        $store = new DriverStoreSingleton();

        $store->isEnabled('test-driver-1');
    }

    /** @test */
    public function all_returns_enabled_and_disabled_drivers(){
        $store = new DriverStoreSingleton();

        $store->register('test-driver-1', fn() => 'this is a test', true);
        $store->register('test-driver-2', fn() => 'this is a test', false);
        $store->register('test-driver-3', fn() => 'this is a test', true);
        $store->register('test-driver-4', fn() => 'this is a test', false);

        $this->assertEquals([
            'test-driver-1',
            'test-driver-2',
            'test-driver-3',
            'test-driver-4'
        ], $store->all());
    }


    /** @test */
    public function allEnabled_returns_only_enabled_drivers(){
        $store = new DriverStoreSingleton();

        $store->register('test-driver-1', fn() => 'this is a test', true);
        $store->register('test-driver-2', fn() => 'this is a test', false);
        $store->register('test-driver-3', fn() => 'this is a test', true);
        $store->register('test-driver-4', fn() => 'this is a test', false);

        $this->assertEquals([
            'test-driver-1',
            'test-driver-3'
        ], $store->allEnabled());
    }

    /** @test */
    public function hasDriver_returns_true_if_a_driver_exists(){
        $store = new DriverStoreSingleton();

        $store->register('test-driver-1', fn() => 'this is a test', true);

        $this->assertTrue($store->hasDriver('test-driver-1'));
    }

    /** @test */
    public function hasDriver_returns_false_if_a_driver_does_not_exist(){
        $store = new DriverStoreSingleton();
        $this->assertFalse($store->hasDriver('test-driver-2'));
    }
}
