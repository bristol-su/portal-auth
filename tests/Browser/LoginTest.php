<?php

namespace BristolSU\Auth\Tests\Browser;

use BristolSU\Auth\Tests\BrowserTestCase;
use Laravel\Dusk\Browser;

class LoginTest extends BrowserTestCase
{

    /** @test */
    public function the_login_page_can_be_loaded(){
        $this->browse(function(Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Email');
        });
    }

}