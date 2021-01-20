<?php

namespace BristolSU\Auth\Tests\Unit\Social\Settings\Providers;

use BristolSU\Auth\Social\Settings\Providers\BaseProviderClientIdSetting;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Str;

class BaseProviderClientIdSettingTest extends TestCase
{
    /** @test */
    public function key_returns_a_proper_key()
    {
        $clientId = new BaseProviderClientIdSettingTestDummyClientIdSetting('test-driver1');

        $this->assertEquals('social-drivers.test-driver1.client_id', $clientId->key());
    }

    /** @test */
    public function defaultValue_returns_null()
    {
        $clientId = new BaseProviderClientIdSettingTestDummyClientIdSetting('test-driver');
        $this->assertNull($clientId->defaultValue());
    }

    /** @test */
    public function rules_allow_a_string_of_reasonable_length()
    {
        $clientId = new BaseProviderClientIdSettingTestDummyClientIdSetting('test-driver');
        $this->assertTrue($clientId->validator(Str::random(401))->fails());
        $this->assertTrue($clientId->validator(Str::random(402))->fails());
        $this->assertTrue($clientId->validator(Str::random(410))->fails());
        $this->assertTrue($clientId->validator(Str::random(2))->fails());
        $this->assertTrue($clientId->validator(Str::random(1))->fails());
        $this->assertTrue($clientId->validator(558465)->fails());
        $this->assertTrue($clientId->validator(11)->fails());
        $this->assertTrue($clientId->validator(['some array'])->fails());
        $this->assertTrue($clientId->validator(null)->fails());

        $this->assertFalse($clientId->validator(Str::random(400))->fails());
        $this->assertFalse($clientId->validator(Str::random(399))->fails());
        $this->assertFalse($clientId->validator(Str::random(3))->fails());
        $this->assertFalse($clientId->validator(Str::random(4))->fails());
    }

    /** @test */
    public function it_encrypts_the_setting()
    {
        $clientId = new BaseProviderClientIdSettingTestDummyClientIdSetting('test-driver');
        $this->assertTrue($clientId->shouldEncrypt());
    }

    /** @test */
    public function fieldOptions_returns_a_field()
    {
        $clientId = new BaseProviderClientIdSettingTestDummyClientIdSetting('test-driver');
        $this->assertInstanceOf(Field::class, $clientId->fieldOptions());
    }



}

class BaseProviderClientIdSettingTestDummyClientIdSetting extends BaseProviderClientIdSetting
{

    public function __construct(protected string $driver)
    {
    }

    public function driver(): string
    {
        return $this->driver;
    }
}
