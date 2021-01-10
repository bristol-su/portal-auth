<?php


namespace BristolSU\Auth\Tests\Integration\Http\Controllers\Auth;


use BristolSU\Auth\Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{

    /** @test */
    public function showForm_throws_an_exception_if_the_link_uuid_is_not_valid(){

    }

    /** @test */
    public function showForm_a_link_can_only_be_used_once(){

    }

    /** @test */
    public function showForm_returns_an_exception_if_a_user_is_logged_in(){

    }

    /** @test */
    public function showForm_returns_the_correct_view(){

    }

    /** @test */
    public function showForm_passes_the_correct_email(){

    }

    /** @test */
    public function showForm_passes_a_reset_link_url_that_allows_resetPassword_to_be_called(){

    }

    /** @test */
    public function resetPassword_throws_an_exception_if_the_link_uuid_is_not_valid(){

    }

    /** @test */
    public function resetPassword_returns_an_exception_if_a_user_is_logged_in(){

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_is_not_given(){

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_confirmation_is_not_given(){

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_and_password_confirmation_are_not_given(){

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_and_password_confirmation_are_not_the_same(){

    }

    /** @test */
    public function resetPassword_fails_validation_if_the_password_is_less_than_6_characters(){

    }

    /** @test */
    public function resetPassword_passes_validation_if_the_password_is_equal_to_6_characters(){

    }

    /** @test */
    public function resetPassword_passes_validation_if_the_password_is_greater_than_to_6_characters(){

    }

    /** @test */
    public function resetPassword_is_throttled_to_3_a_minute(){

    }

    /** @test */
    public function resetPassword_a_link_can_be_used_many_times(){

    }

    /** @test */
    public function resetPassword_changes_the_password_of_the_user(){

    }

    /** @test */
    public function resetPassword_logs_the_user_in(){

    }

    /** @test */
    public function resetPassword_logs_the_user_in_using_authentication(){

    }

    /** @test */
    public function resetPassword_fires_a_PasswordHasBeenReset_event(){

    }

    /** @test */
    public function resetPassword_returns_to_the_default_home_for_the_user(){

    }





}
