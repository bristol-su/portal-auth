<?php

namespace BristolSU\Auth\Tests\Unit\Settings;

use BristolSU\Auth\Settings\AuthCategory;
use BristolSU\Auth\Tests\TestCase;

class AuthCategoryTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new AuthCategory();
        $this->assertInstanceOf(AuthCategory::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new AuthCategory();
        $this->assertEquals('authentication', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new AuthCategory();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new AuthCategory();
        $this->assertIsString($category->description());
    }

}
