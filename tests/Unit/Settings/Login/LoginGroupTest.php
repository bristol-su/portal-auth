<?php


namespace BristolSU\Auth\Tests\Unit\Settings\Login;


use BristolSU\Auth\Settings\Login\LoginGroup;
use BristolSU\Auth\Tests\TestCase;

class LoginGroupTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new LoginGroup();
        $this->assertInstanceOf(LoginGroup::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new LoginGroup();
        $this->assertEquals('authentication.login', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new LoginGroup();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new LoginGroup();
        $this->assertIsString($category->description());
    }

}
