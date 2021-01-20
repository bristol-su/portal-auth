<?php

namespace BristolSU\Auth\Tests\Unit\Social\Settings\Providers;

use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientId;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubClientSecret;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubEnabled;
use BristolSU\Auth\Social\Settings\Providers\Github\GithubGroup;
use BristolSU\Auth\Tests\TestCase;

class GithubProviderTest extends TestCase
{

    /** @test */
    public function ClientId_returns_the_correct_driver(){
        $clientId = new GithubClientId();
        $this->assertEquals('github', $clientId->driver());
    }

    /** @test */
    public function ClientSecret_returns_the_correct_driver(){
        $clientId = new GithubClientSecret();
        $this->assertEquals('github', $clientId->driver());
    }

    /** @test */
    public function Enabled_returns_the_correct_driver(){
        $clientId = new GithubEnabled();
        $this->assertEquals('github', $clientId->driver());
    }

    /** @test */
    public function Group_returns_the_correct_driver(){
        $clientId = new GithubGroup();
        $this->assertEquals('github', $clientId->driver());
    }

}
