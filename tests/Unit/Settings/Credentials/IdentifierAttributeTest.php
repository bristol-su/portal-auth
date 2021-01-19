<?php

namespace BristolSU\Auth\Tests\Unit\Settings\Credentials;

use BristolSU\Auth\Settings\Credentials\IdentifierAttribute;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;

class IdentifierAttributeTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $setting = new IdentifierAttribute();
        $this->assertInstanceOf(IdentifierAttribute::class, $setting);
    }

    /** @test */
    public function rules_returns_the_rules_for_the_identifier(){
        $setting = new IdentifierAttribute();
        $rules = $setting->rules();
        $this->assertArrayHasKey($setting->inputName(), $rules);
        $this->assertContains('string', $rules[$setting->inputName()]);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $setting = new IdentifierAttribute();
        $this->assertEquals('authentication.credentials.identifier', $setting->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $setting = new IdentifierAttribute();
        $this->assertEquals('email', $setting->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $setting = new IdentifierAttribute();
        $this->assertInstanceOf(Field::class, $setting->fieldOptions());
    }

}
