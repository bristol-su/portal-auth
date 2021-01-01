<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Credentials;

use BristolSU\Auth\Settings\Credentials\CredentialsGroup;
use BristolSU\Auth\Tests\TestCase;

class CredentialsGroupTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new CredentialsGroup();
        $this->assertInstanceOf(CredentialsGroup::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new CredentialsGroup();
        $this->assertEquals('authentication.credentials', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new CredentialsGroup();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new CredentialsGroup();
        $this->assertIsString($category->description());
    }

}
