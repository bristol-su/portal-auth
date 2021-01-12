<?php


namespace BristolSU\Auth\Tests\Unit\Settings\Access;


use BristolSU\Auth\Settings\Access\AccessGroup;
use BristolSU\Auth\Tests\TestCase;

class AccessGroupTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new AccessGroup();
        $this->assertInstanceOf(AccessGroup::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new AccessGroup();
        $this->assertEquals('authentication.access', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new AccessGroup();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new AccessGroup();
        $this->assertIsString($category->description());
    }

}
