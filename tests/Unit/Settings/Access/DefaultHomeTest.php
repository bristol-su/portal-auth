<?php


namespace BristolSU\Auth\Tests\Unit\Settings\Access;


use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Auth\Tests\TestCase;
use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Route;

class DefaultHomeTest extends TestCase
{

    /** @test */
    public function the_class_can_be_created(){
        $defaultHome = new DefaultHome();
        $this->assertInstanceOf(DefaultHome::class, $defaultHome);
    }

    /** @test */
    public function key_returns_the_setting_key(){
        $defaultHome = new DefaultHome();
        $this->assertEquals('authentication.access.default-home', $defaultHome->key());
    }

    /** @test */
    public function defaultValue_returns_the_default_setting_value(){
        $defaultHome = new DefaultHome();
        $this->assertEquals('portal', $defaultHome->defaultValue());
    }

    /** @test */
    public function fieldOptions_returns_a_field_instance(){
        $defaultHome = new DefaultHome();
        $this->assertInstanceOf(Field::class, $defaultHome->fieldOptions());
    }

    /** @test */
    public function validation_fails_if_an_integer_is_given(){
        $defaultHome = new DefaultHome();

        $validator = $defaultHome->validator(2233);
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function validation_fails_if_an_invalid_route_name_is_given(){
        $defaultHome = new DefaultHome();

        $validator = $defaultHome->validator('jkfghdkghkjdfbvjhfbkvhbkrybvukyerkvuykebryvbekuyv');
        $this->assertTrue($validator->fails());

        $this->assertEquals(
            'The default home is not a valid route name.',
            $validator->errors()->get($defaultHome->inputName())[0]
        );
    }

    /** @test */
    public function validation_passes_if_a_valid_route_name_is_given(){


        $defaultHome = new DefaultHome();

        Route::name('test-name')->get('test', fn($request) => response('Test', 200));

        $validator = $defaultHome->validator('test-name');
        $this->assertFalse($validator->fails());
    }

}
