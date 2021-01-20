<?php


namespace BristolSU\Auth\Tests\Unit\Social\Http\Middleware;


use BristolSU\Auth\Social\Driver\DriverLoader;
use BristolSU\Auth\Social\Http\Middleware\LoadsSocialite;
use BristolSU\Auth\Tests\TestCase;
use Illuminate\Http\Request;

class LoadsSocialiteTest extends TestCase
{

    /** @test */
    public function it_loads_all_enabled_drivers(){
        $loader = $this->prophesize(DriverLoader::class);
        $loader->loadAllEnabled()->shouldBeCalled();

        $request = $this->prophesize(Request::class);

        $middleware = new LoadsSocialite($loader->reveal());
        $middleware->handle($request->reveal(), fn($request) => 'test');
    }

    /** @test */
    public function it_calls_the_callback(){
        $loader = $this->prophesize(DriverLoader::class);
        $loader->loadAllEnabled()->shouldBeCalled();

        $request = $this->prophesize(Request::class);

        $middleware = new LoadsSocialite($loader->reveal());
        $this->assertEquals('test',
            $middleware->handle($request->reveal(), fn($request) => 'test')
        );
    }

}
