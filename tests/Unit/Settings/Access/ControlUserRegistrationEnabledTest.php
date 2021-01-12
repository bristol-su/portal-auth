<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Access;

use BristolSU\Auth\Settings\Access\ControlUserRegistrationEnabled;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class ControlUserRegistrationEnabledTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $registrationEnabled = new ControlUserRegistrationEnabled();
        $this->assertInstanceOf(ControlUserRegistrationEnabled::class, $registrationEnabled);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $registrationEnabled = new ControlUserRegistrationEnabled();
        $this->assertEquals('authentication.access.controluser-registration-enabled', $registrationEnabled->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $registrationEnabled = new ControlUserRegistrationEnabled();
        $this->assertEquals(true, $registrationEnabled->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $registrationEnabled = new ControlUserRegistrationEnabled();
        $this->assertInstanceOf(Field::class, $registrationEnabled->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_an_integer_is_given(){
        $registrationEnabled = new ControlUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(2233);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_a_string_is_given(){
        $registrationEnabled = new ControlUserRegistrationEnabled();

        $validator = $registrationEnabled->validator('ssrefgwq');
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_false_is_given(){

        $registrationEnabled = new ControlUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(false);
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validation_passes_if_true_is_given(){

        $registrationEnabled = new ControlUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(true);
        $this->assertFalse($validator->fails());
    }


}
