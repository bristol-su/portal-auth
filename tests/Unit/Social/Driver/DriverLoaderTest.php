<?php


namespace BristolSU\Auth\Tests\Unit\Social\Driver;


use BristolSU\Auth\Social\Driver\DriverLoader;
use BristolSU\Auth\Social\Driver\DriverStore;
use BristolSU\Auth\Tests\TestCase;

class DriverLoaderTest extends TestCase
{


    /** @test */
    public function load_throws_an_exception_if_the_driver_not_found(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test Message');

        $store = $this->prophesize(DriverStore::class);
        $store->getSetup('test-driver')->shouldBeCalled()->willThrow(new \Exception('Test Message'));

        $loader = new DriverLoader($store->reveal());
        $loader->load('test-driver');

    }

    /** @test */
    public function load_calls_the_driver_callback(){
        $hasBeenCalled = false;
        $store = $this->prophesize(DriverStore::class);
        $store->getSetup('test-driver')->shouldBeCalled()->willReturn(function() use (&$hasBeenCalled) {
            $hasBeenCalled = true;
        });

        $loader = new DriverLoader($store->reveal());
        $loader->load('test-driver');

        $this->assertTrue($hasBeenCalled);
    }

    /** @test */
    public function loadAllEnabled_calls_the_callback_for_all_enabled_drivers(){
        $hasBeenCalled1 = false;
        $hasBeenCalled2 = false;
        $hasBeenCalled3 = false;
        $store = $this->prophesize(DriverStore::class);
        $store->getSetup('test-driver-1')->shouldBeCalled()->willReturn(function() use (&$hasBeenCalled1) {
            $hasBeenCalled1 = true;
        });
        $store->getSetup('test-driver-2')->shouldBeCalled()->willReturn(function() use (&$hasBeenCalled2) {
            $hasBeenCalled2 = true;
        });
        $store->getSetup('test-driver-3')->shouldBeCalled()->willReturn(function() use (&$hasBeenCalled3) {
            $hasBeenCalled3 = true;
        });
        $store->allEnabled()->shouldBeCalled()->willReturn(['test-driver-1', 'test-driver-2', 'test-driver-3']);

        $loader = new DriverLoader($store->reveal());
        $loader->loadAllEnabled();

        $this->assertTrue($hasBeenCalled1);
        $this->assertTrue($hasBeenCalled2);
        $this->assertTrue($hasBeenCalled3);
    }

}
