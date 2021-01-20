<?php

namespace BristolSU\Auth\Tests\Unit\Social\Settings\Providers;

use BristolSU\Auth\Social\Settings\Providers\BaseProviderClientSecretSetting;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Str;

class BaseProviderClientSecretSettingTest extends TestCase
{
    /** @test */
    public function key_returns_a_proper_key()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyClientSecretSetting('test-driver1');

        $this->assertEquals('social-drivers.test-driver1.client_secret', $clientSecret->key());
    }

    /** @test */
    public function defaultValue_returns_null()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyClientSecretSetting('test-driver');
        $this->assertNull($clientSecret->defaultValue());
    }

    /** @test */
    public function rules_allow_a_string_of_reasonable_length()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyClientSecretSetting('test-driver');
        $this->assertTrue($clientSecret->validator(Str::random(401))->fails());
        $this->assertTrue($clientSecret->validator(Str::random(402))->fails());
        $this->assertTrue($clientSecret->validator(Str::random(410))->fails());
        $this->assertTrue($clientSecret->validator(Str::random(2))->fails());
        $this->assertTrue($clientSecret->validator(Str::random(1))->fails());
        $this->assertTrue($clientSecret->validator(558465)->fails());
        $this->assertTrue($clientSecret->validator(11)->fails());
        $this->assertTrue($clientSecret->validator(['some array'])->fails());
        $this->assertTrue($clientSecret->validator(null)->fails());

        $this->assertFalse($clientSecret->validator(Str::random(400))->fails());
        $this->assertFalse($clientSecret->validator(Str::random(399))->fails());
        $this->assertFalse($clientSecret->validator(Str::random(3))->fails());
        $this->assertFalse($clientSecret->validator(Str::random(4))->fails());
    }

    /** @test */
    public function it_encrypts_the_setting()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyClientSecretSetting('test-driver');
        $this->assertTrue($clientSecret->shouldEncrypt());
    }

    /** @test */
    public function fieldOptions_returns_a_field()
    {
        $clientSecret = new BaseProviderClientSecretSettingTestDummyClientSecretSetting('test-driver');
        $this->assertInstanceOf(Field::class, $clientSecret->fieldOptions());
    }



}

class BaseProviderClientSecretSettingTestDummyClientSecretSetting extends BaseProviderClientSecretSetting
{

    public function __construct(protected string $driver)
    {
    }

    public function driver(): string
    {
        return $this->driver;
    }
}
