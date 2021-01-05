<?php


namespace BristolSU\Auth\Tests\Unit\Exceptions;


use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Exceptions\Handler;
use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Tests\TestCase;
use Illuminate\Http\Request;

class HandlerTest extends TestCase
{

    /** @test */
    public function it_handles_a_web_request_throwing_an_EmailNotVerified_exception(){
        $handler = new Handler($this->app);

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);

        $exception = new EmailNotVerified();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertTrue($response->isRedirect('http://localhost/verify'));
    }

    /** @test */
    public function it_handles_a_json_request_throwing_an_EmailNotVerified_exception(){
        $handler = new Handler($this->app);

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);

        $exception = new EmailNotVerified();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertEquals('"You must verify your email address."', $response->getContent());
    }

    /** @test */
    public function it_handles_a_json_request_throwing_a_PasswordUnconfirmed_exception(){
        $handler = new Handler($this->app);

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);

        $exception = new PasswordUnconfirmed();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertEquals('"Password confirmation required."', $response->getContent());
    }

    /** @test */
    public function it_lets_through_another_exception_for_a_web_request(){
        $handler = new Handler($this->app);

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);

        $exception = new \Exception();
        $response = $handler->render($request->reveal(), $exception);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function it_lets_through_another_exception_for_a_json_request(){
        $handler = new Handler($this->app);

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);

        $exception = new \Exception();
        $response = $handler->render($request->reveal(), $exception);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
