<?php


namespace BristolSU\Auth\Tests\Unit\Social\Settings;


use BristolSU\Auth\Social\Settings\BaseSocialDriversGroup;
use BristolSU\Auth\Tests\TestCase;

class BaseSocialDriversGroupTest extends TestCase
{

    /** @test */
    public function it_returns_the_correct_key_for_the_driver(){
        $group = new BaseSocialDriversGroupTestDummySocialDriversGroup('some-driver');
        $this->assertEquals('social-drivers.some-driver', $group->key());
    }

    /**
     * @test
     * @dataProvider driverTitles
     */
    public function it_returns_a_formatted_name_for_the_driver(string $driver, string $formatted){
        $group = new BaseSocialDriversGroupTestDummySocialDriversGroup($driver);
        $this->assertEquals(sprintf('%s Driver', $formatted), $group->name());
    }

    /**
     * @test
     * @dataProvider driverTitles
     */
    public function it_returns_a_formatted_description_for_the_driver(string $driver, string $formatted){
        $group = new BaseSocialDriversGroupTestDummySocialDriversGroup($driver);
        $this->assertEquals(sprintf('Set up the authentication integration with %s.', $formatted), $group->description());
    }

    public function driverTitles(): array
    {
        return [
            ['test-driver', 'Test Driver'],
            ['Microsoft azure Platform', 'Microsoft Azure Platform'],
            ['microsoft-azure-platform', 'Microsoft Azure Platform'],
            ['microsoft_teams', 'Microsoft Teams'],
        ];
    }

}

class BaseSocialDriversGroupTestDummySocialDriversGroup extends BaseSocialDriversGroup
{

    public function __construct(protected string $driver)
    {
    }

    public function driver(): string
    {
        return $this->driver;
    }
}
