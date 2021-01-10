<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{

    /** @test */
    public function showForm_redirects_to_the_homepage_if_a_user_is_logged_in(){

    }

    /** @test */
    public function sendResetLink_redirects_to_the_homepage_if_a_user_is_logged_in(){

    }

    /** @test */
    public function showForm_returns_the_correct_view(){

    }

    /** @test */
    public function sendResetLink_is_throttled_to_3_a_minute(){

    }

    /** @test */
    public function sendResetLink_fails_validation_if_the_identifier_is_not_given(){

    }

    /** @test */
    public function sendResetLink_validates_if_the_data_user_with_the_given_attribute_does_not_exist(){

    }

    /** @test */
    public function sendResetLink_validates_if_the_control_user_for_the_data_user_does_not_exist(){

    }

    /** @test */
    public function sendResetLink_validates_if_the_authentication_user_for_the_control_user_does_not_exist(){

    }

    /** @test */
    public function it_fires_an_event_with_the_correct_user(){

    }

    /** @test */
    public function it_redirects_to_showForm(){

    }



}
