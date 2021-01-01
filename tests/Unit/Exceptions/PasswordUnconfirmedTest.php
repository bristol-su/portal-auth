<?php

namespace BristolSU\Auth\Tests\Unit\Exceptions;

use BristolSU\Auth\Exceptions\PasswordUnconfirmed;
use BristolSU\Auth\Tests\TestCase;

class PasswordUnconfirmedTest extends TestCase
{

    /** @test */
    public function it_has_a_default_message_and_code(){
        $exception = new PasswordUnconfirmed();

        $this->assertEquals('Password confirmation required', $exception->getMessage());
        $this->assertEquals(423, $exception->getCode());
    }

}
