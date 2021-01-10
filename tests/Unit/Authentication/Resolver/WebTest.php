<?php

namespace BristolSU\Auth\Tests\Unit\Authentication\Resolver;

use BristolSU\Auth\Authentication\Resolver\Web;
use BristolSU\Auth\Tests\TestCase;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Prophecy\Argument;

class WebTest extends TestCase
{

    /** @test */
    public function getUser_retrieves_a_user_from_the_web_guard(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $auth = resolve(Web::class);
        $this->assertModelEquals($user, $auth->getUser());
    }

    /** @test */
    public function getUser_returns_null_if_no_user_found(){
        $factory = $this->prophesize(Factory::class);
        $guard = $this->prophesize(Guard::class);
        $guard->check()->shouldBeCalled()->willReturn(false);
        $factory->guard('web')->shouldBeCalled()->willReturn($guard->reveal());

        $auth = new Web($factory->reveal());
        $this->assertNull($auth->getUser());
    }

    /** @test */
    public function setUser_sets_the_user(){
        $user = AuthenticationUser::factory()->create();

        $factory = $this->prophesize(Factory::class);
        $guard = $this->prophesize(StatefulGuard::class);
        $guard->login(Argument::that(function($arg) use ($user) {
            return $arg instanceof AuthenticationUser && $arg->is($user);
        }))->shouldBeCalled()->willReturn(false);
        $factory->guard('web')->shouldBeCalled()->willReturn($guard->reveal());

        $auth = new Web($factory->reveal());
        $auth->setUser($user);
    }

    /** @test */
    public function logout_logs_out(){
        $factory = $this->prophesize(Factory::class);
        /** @var Guard $guard */
        $guard = $this->prophesize(StatefulGuard::class);
        $guard->logout()->shouldBeCalled();
        $factory->guard('web')->shouldBeCalled()->willReturn($guard->reveal());

        $auth = new Web($factory->reveal());
        $auth->logout();
    }

    /** @test */
    public function hasUser_returns_true_if_user_exists(){
        $user = AuthenticationUser::factory()->create();
        $this->be($user, 'web');

        $auth = resolve(Web::class);
        $this->assertTrue($auth->hasUser());
    }

    /** @test */
    public function hasUser_returns_false_if_no_user_found(){
        $factory = $this->prophesize(Factory::class);
        $guard = $this->prophesize(Guard::class);
        $guard->check()->shouldBeCalled()->willReturn(false);
        $factory->guard('web')->shouldBeCalled()->willReturn($guard->reveal());

        $auth = new Web($factory->reveal());
        $this->assertFalse($auth->hasUser());
    }

}
