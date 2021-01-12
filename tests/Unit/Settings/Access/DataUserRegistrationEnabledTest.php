<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Access;

use BristolSU\Auth\Settings\Access\DataUserRegistrationEnabled;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class DataUserRegistrationEnabledTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $registrationEnabled = new DataUserRegistrationEnabled();
        $this->assertInstanceOf(DataUserRegistrationEnabled::class, $registrationEnabled);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $registrationEnabled = new DataUserRegistrationEnabled();
        $this->assertEquals('authentication.access.datauser-registration-enabled', $registrationEnabled->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $registrationEnabled = new DataUserRegistrationEnabled();
        $this->assertEquals(true, $registrationEnabled->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $registrationEnabled = new DataUserRegistrationEnabled();
        $this->assertInstanceOf(Field::class, $registrationEnabled->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_an_integer_is_given(){
        $registrationEnabled = new DataUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(2233);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_a_string_is_given(){
        $registrationEnabled = new DataUserRegistrationEnabled();

        $validator = $registrationEnabled->validator('ssrefgwq');
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_false_is_given(){

        $registrationEnabled = new DataUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(false);
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validation_passes_if_true_is_given(){

        $registrationEnabled = new DataUserRegistrationEnabled();

        $validator = $registrationEnabled->validator(true);
        $this->assertFalse($validator->fails());
    }


}
