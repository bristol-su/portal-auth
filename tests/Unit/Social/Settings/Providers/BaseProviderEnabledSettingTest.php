<?php

namespace BristolSU\Auth\Tests\Unit\Social\Settings\Providers;

use BristolSU\Auth\Social\Settings\Providers\BaseProviderEnabledSetting;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;

class BaseProviderEnabledSettingTest extends TestCase
{

    /** @test */
    public function key_returns_a_proper_key()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyEnabledSetting('test-driver1');

        $this->assertEquals('social-drivers.test-driver1.enabled', $clientSecret->key());
    }

    /** @test */
    public function defaultValue_returns_false()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyEnabledSetting('test-driver');
        $this->assertFalse($clientSecret->defaultValue());
    }

    /** @test */
    public function the_rules_allow_a_boolean()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyEnabledSetting('test-driver');
        $this->assertTrue($clientSecret->validator('a-string')->fails());
        $this->assertTrue($clientSecret->validator(887)->fails());
        $this->assertTrue($clientSecret->validator(['some array'])->fails());
        $this->assertTrue($clientSecret->validator(null)->fails());

        $this->assertFalse($clientSecret->validator(1)->fails());
        $this->assertFalse($clientSecret->validator(0)->fails());
        $this->assertFalse($clientSecret->validator(true)->fails());
        $this->assertFalse($clientSecret->validator(false)->fails());
    }

    /** @test */
    public function it_does_not_encrypt_the_setting()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyEnabledSetting('test-driver');
        $this->assertFalse($clientSecret->shouldEncrypt());
    }

    /** @test */
    public function fieldOptions_returns_a_field()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyEnabledSetting('test-driver');
        $this->assertInstanceOf(Field::class, $clientSecret->fieldOptions());
    }

}

class BaseProviderClientSecretSettingTestDummyEnabledSetting extends BaseProviderEnabledSetting
{

    public function __construct(protected string $driver)
    {
    }

    public function driver(): string
    {
        return $this->driver;
    }
}
