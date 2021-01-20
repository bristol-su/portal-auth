<?php


namespace BristolSU\Auth\Tests\Unit\Social;


use BristolSU\Auth\Social\NameParser;
use BristolSU\Auth\Tests\TestCase;

class NameParserTest extends TestCase
{

    /**
     * @test
     * @dataProvider nameProvider
     */
    public function it_can_split_the_name_accurately(string $fullName, string $firstName, string $lastName){
        $nameParser = new NameParser($fullName);
        $this->assertEquals($firstName, $nameParser->getFirstName());
        $this->assertEquals($lastName, $nameParser->getLastName());
    }

    /** @test */
    public function the_order_of_the_split_doesnt_matter_and_we_can_call_it_multiple_times(){
        $nameParser = new NameParser('Joe Smith');
        $this->assertEquals('Smith', $nameParser->getLastName());
        $this->assertEquals('Joe', $nameParser->getFirstName());
        $this->assertEquals('Joe', $nameParser->getFirstName());
        $this->assertEquals('Smith', $nameParser->getLastName());
    }

    /** @test */
    public function it_can_be_created(){
        $nameParser = new NameParser('Joe Smith');
        $this->assertInstanceOf(NameParser::class, $nameParser);
        $this->assertEquals('Joe', $nameParser->getFirstName());
        $this->assertEquals('Smith', $nameParser->getLastName());
    }

    /** @test */
    public function it_can_be_created_using_the_parse_function(){
        $nameParser = NameParser::parse('Joe Smith');
        $this->assertInstanceOf(NameParser::class, $nameParser);
        $this->assertEquals('Joe', $nameParser->getFirstName());
        $this->assertEquals('Smith', $nameParser->getLastName());
    }

    public function nameProvider(): array
    {
        return [
            ['Toby Twigger', 'Toby', 'Twigger'],
            ['John Smith', 'John', 'Smith'],
            ['Alexander de Pfeffel', 'Alexander', 'de Pfeffel']
        ];
    }

}
