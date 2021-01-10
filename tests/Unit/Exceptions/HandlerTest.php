<?php


namespace BristolSU\Auth\Tests\Unit\Exceptions;


use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Exceptions\Handler;
use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Tests\TestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Linkeys\UrlSigner\Exceptions\LinkNotFoundException;
use Prophecy\Argument;

class HandlerTest extends TestCase
{

    /** @test */
    public function it_handles_a_web_request_throwing_an_EmailNotVerified_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');

        $exception = new EmailNotVerified();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertTrue($response->isRedirect('http://localhost/verify'));
    }

    /** @test */
    public function it_handles_a_json_request_throwing_an_EmailNotVerified_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);
        $request->path()->willReturn('test1');

        $exception = new EmailNotVerified();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertEquals('"You must verify your email address."', $response->getContent());
    }

    /** @test */
    public function it_handles_a_web_request_throwing_an_PasswordUnconfirmed_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');

        $exception = new PasswordUnconfirmed();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertTrue($response->isRedirect('http://localhost/password/confirm'));
    }

    /** @test */
    public function it_handles_a_json_request_throwing_a_PasswordUnconfirmed_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);
        $request->path()->willReturn('test1');

        $exception = new PasswordUnconfirmed();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertEquals('"Password confirmation required."', $response->getContent());
    }

    /** @test */
    public function it_lets_through_another_exception_for_a_web_request(){
        $exception = new \Exception();
        $prophetResponse = $this->prophesize(Response::class);
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler->render(Argument::any(), $exception)->shouldBeCalled()->willReturn($prophetResponse->reveal());
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');

        $response = $handler->render($request->reveal(), $exception);
        $this->assertEquals($prophetResponse->reveal(), $response);
    }

    /** @test */
    public function it_lets_through_another_exception_for_a_json_request(){
        $exception = new \Exception();
        $prophetResponse = $this->prophesize(Response::class);
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler->render(Argument::any(), $exception)->shouldBeCalled()->willReturn($prophetResponse->reveal());
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(true);
        $request->path()->willReturn('test1');

        $response = $handler->render($request->reveal(), $exception);
        $this->assertEquals($prophetResponse->reveal(), $response);
    }

    /** @test */
    public function it_handles_a_web_request_throwing_an_LinkNotValid_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $session = $this->prophesize(Store::class);
        $session->flash('messages', 'This link has expired.')->shouldBeCalled();

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');
        $request->session()->willReturn($session->reveal());

        $exception = new LinkNotFoundException();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertTrue($response->isRedirect('http://localhost/verify'));
    }

    /** @test */
    public function it_handles_a_json_request_throwing_an_LinkNotValid_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->path()->willReturn('test1');
        $request->expectsJson()->willReturn(true);

        $exception = new LinkNotFoundException();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertEquals('"This link has expired."', $response->getContent());
    }

    /** @test */
    public function the_intended_url_is_set_for_a_web_PasswordUnconfirmed_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');

        $this->assertArrayNotHasKey('url.intended', Session::all());

        $exception = new PasswordUnconfirmed();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertArrayHasKey('url', Session::all());
        $this->assertArrayHasKey('intended', Session::all()['url']);
        $this->assertEquals('test1', Session::get('url.intended'));
    }

    /** @test */
    public function the_intended_url_is_set_for_a_web_EmailNotVerified_exception(){
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler = new Handler($handler->reveal());

        $request = $this->prophesize(Request::class);
        $request->expectsJson()->willReturn(false);
        $request->path()->willReturn('test1');

        $this->assertArrayNotHasKey('url.intended', Session::all());

        $exception = new EmailNotVerified();
        $response = $handler->render($request->reveal(), $exception);

        $this->assertArrayHasKey('url', Session::all());
        $this->assertArrayHasKey('intended', Session::all()['url']);
        $this->assertEquals('test1', Session::get('url.intended'));
    }

    /** @test */
    public function shouldReport_calls_the_underlying_handler_instance(){
        $exception = new \Exception();
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler->shouldReport($exception)->shouldBeCalled()->willReturn('test');
        $handler = new Handler($handler->reveal());

        $handler->shouldReport($exception);
    }

    /** @test */
    public function renderForConsole_calls_the_underlying_handler_instance(){
        $exception = new \Exception();
        $handler = $this->prophesize(ExceptionHandler::class);
        $handler->renderForConsole('output', $exception)->shouldBeCalled()->willReturn('test');
        $handler = new Handler($handler->reveal());

        $handler->renderForConsole('output', $exception);
    }

}
