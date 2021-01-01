<?php

namespace Database\Auth\Factories;

use BristolSU\Auth\User\AuthenticationUser;
use BristolSU\ControlDB\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AuthenticationUserFactory extends Factory
{

    protected $model = AuthenticationUser::class;

    public function definition()
    {
        return [
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'control_id' => function () {
                return factory(User::class)->create()->id();
            },
        ];
    }
}
