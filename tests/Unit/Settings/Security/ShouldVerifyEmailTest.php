<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Security;

use BristolSU\Auth\Settings\Security\ShouldVerifyEmail;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class ShouldVerifyEmailTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();
        $this->assertInstanceOf(ShouldVerifyEmail::class, $passwordConfirmationTimeout);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();
        $this->assertEquals('authentication.security.should-verify-email', $passwordConfirmationTimeout->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();
        $this->assertEquals(true, $passwordConfirmationTimeout->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();
        $this->assertInstanceOf(Field::class, $passwordConfirmationTimeout->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_a_string_is_given(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();

        $validator = $passwordConfirmationTimeout->validator('test123');
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_passes_if_true_is_given(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();

        $validator = $passwordConfirmationTimeout->validator(true);
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function validation_passes_if_false_is_given(){
        $passwordConfirmationTimeout = new ShouldVerifyEmail();

        $validator = $passwordConfirmationTimeout->validator(false);
        $this->assertFalse($validator->fails());
    }


}
