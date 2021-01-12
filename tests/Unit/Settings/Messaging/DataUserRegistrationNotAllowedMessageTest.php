<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Messaging;

use BristolSU\Auth\Settings\Messaging\DataUserRegistrationNotAllowedMessage;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class DataUserRegistrationNotAllowedMessageTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();
        $this->assertInstanceOf(DataUserRegistrationNotAllowedMessage::class, $registrationNotAllowed);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();
        $this->assertEquals('authentication.messaging.datauser-registration-not-allowed-message', $registrationNotAllowed->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();
        $this->assertEquals('Registration is currently closed to new users.', $registrationNotAllowed->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();
        $this->assertInstanceOf(Field::class, $registrationNotAllowed->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_an_integer_is_given(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();

        $validator = $registrationNotAllowed->validator(2233);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_a_boolean_is_given(){
        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();

        $validator = $registrationNotAllowed->validator(true);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_a_string_is_given(){

        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();

        $validator = $registrationNotAllowed->validator('test123');
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validation_fails_if_null_is_given(){

        $registrationNotAllowed = new DataUserRegistrationNotAllowedMessage();

        $validator = $registrationNotAllowed->validator(null);
        $this->assertTrue($validator->fails());
    }


}
