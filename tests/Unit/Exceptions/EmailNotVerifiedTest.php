<?php

namespace BristolSU\Auth\Tests\Unit\Exceptions;

use BristolSU\Auth\Exceptions\EmailNotVerified;
use BristolSU\Auth\Tests\TestCase;

class EmailNotVerifiedTest extends TestCase
{

    /** @test */
    public function it_has_a_default_message_and_code(){
        $exception = new EmailNotVerified();

        $this->assertEquals('Email verification required', $exception->getMessage());
        $this->assertEquals(423, $exception->getCode());
    }

}
