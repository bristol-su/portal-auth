<?php


namespace BristolSU\Auth\Tests\Unit\Settings\Security;


use BristolSU\Auth\Settings\Security\SecurityGroup;
use BristolSU\Auth\Tests\TestCase;

class SecurityGroupTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new SecurityGroup();
        $this->assertInstanceOf(SecurityGroup::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new SecurityGroup();
        $this->assertEquals('authentication.security', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new SecurityGroup();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new SecurityGroup();
        $this->assertIsString($category->description());
    }

}
