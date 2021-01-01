<?php

use Illuminate\Database\Migrations\Migration;

class RenameUsersTableToAuthenticationUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::rename('users', 'authentication_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\Schema::rename('authentication_users', 'users');
    }

}
