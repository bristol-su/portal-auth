<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Messaging;

use BristolSU\Auth\Settings\Messaging\AlreadyRegisteredMessage;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class AlreadyRegisteredMessageTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $alreadyRegistered = new AlreadyRegisteredMessage();
        $this->assertInstanceOf(AlreadyRegisteredMessage::class, $alreadyRegistered);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $alreadyRegistered = new AlreadyRegisteredMessage();
        $this->assertEquals('authentication.messaging.already-registered', $alreadyRegistered->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $alreadyRegistered = new AlreadyRegisteredMessage();
        $this->assertEquals('You have already registered!', $alreadyRegistered->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $alreadyRegistered = new AlreadyRegisteredMessage();
        $this->assertInstanceOf(Field::class, $alreadyRegistered->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_an_integer_is_given(){
        $alreadyRegistered = new AlreadyRegisteredMessage();

        $validator = $alreadyRegistered->validator(2233);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_a_boolean_is_given(){
        $alreadyRegistered = new AlreadyRegisteredMessage();

        $validator = $alreadyRegistered->validator(true);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_a_string_is_given(){

        $alreadyRegistered = new AlreadyRegisteredMessage();

        $validator = $alreadyRegistered->validator('test123');
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validation_fails_if_null_is_given(){

        $alreadyRegistered = new AlreadyRegisteredMessage();

        $validator = $alreadyRegistered->validator(null);
        $this->assertTrue($validator->fails());
    }


}
