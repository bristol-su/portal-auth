<?php


namespace BristolSU\Auth\Tests\Unit\Social\Settings;


use BristolSU\Auth\Social\Settings\SocialDriversCategory;
use BristolSU\Auth\Tests\TestCase;

class SocialDriversCategoryTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new SocialDriversCategory();
        $this->assertInstanceOf(SocialDriversCategory::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new SocialDriversCategory();
        $this->assertEquals('social-drivers', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new SocialDriversCategory();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new SocialDriversCategory();
        $this->assertIsString($category->description());
    }

}
