<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Messaging;

use BristolSU\Auth\Settings\Messaging\MessagingGroup;
use BristolSU\Auth\Tests\TestCase;

class MessagingGroupTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $category = new MessagingGroup();
        $this->assertInstanceOf(MessagingGroup::class, $category);
    }

    /** @test */
    public function key_returns_the_key(){
        $category = new MessagingGroup();
        $this->assertEquals('authentication.messaging', $category->key());
    }

    /** @test */
    public function name_returns_a_string(){
        $category = new MessagingGroup();
        $this->assertIsString($category->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $category = new MessagingGroup();
        $this->assertIsString($category->description());
    }

}
