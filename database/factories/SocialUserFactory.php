<?php

namespace Database\Auth\Factories;

use BristolSU\Auth\Social\SocialUser;
use BristolSU\Auth\User\AuthenticationUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SocialUserFactory extends Factory
{

    protected $model = SocialUser::class;

    public function definition()
    {
        return [
            'provider' => $this->faker->randomElement(['github', 'facebook', 'twitter']),
            'provider_id' => Str::random(15),
            'authentication_user_id' => function () {
                return AuthenticationUser::factory()->create()->id();
            },
            'email' => $this->faker->email,
            'name' => $this->faker->name
        ];
    }
}
