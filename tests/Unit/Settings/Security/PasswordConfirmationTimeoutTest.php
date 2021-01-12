<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Security;

use BristolSU\Auth\Settings\Security\PasswordConfirmationTimeout;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class PasswordConfirmationTimeoutTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();
        $this->assertInstanceOf(PasswordConfirmationTimeout::class, $passwordConfirmationTimeout);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();
        $this->assertEquals('authentication.security.password-confirmation-timeout', $passwordConfirmationTimeout->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();
        $this->assertEquals(1800, $passwordConfirmationTimeout->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();
        $this->assertInstanceOf(Field::class, $passwordConfirmationTimeout->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_a_string_is_given(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();

        $validator = $passwordConfirmationTimeout->validator('test123');
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_an_integer_over_24_hours_is_given(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();

        $validator = $passwordConfirmationTimeout->validator(90000);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_a_valid_timeout_is_given(){
        $passwordConfirmationTimeout = new PasswordConfirmationTimeout();

        $validator = $passwordConfirmationTimeout->validator(3600);
        $this->assertFalse($validator->fails());
    }


}
