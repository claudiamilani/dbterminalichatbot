<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace Database\Factories;

use App\Auth\Role;
use App\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'email' => $email = fake()->unique()->safeEmail(),
            'user' => $email,
            'password' => 'M3dialogic$',
            'enabled' => 1,
            'locked' => 0,
            'pwd_change_required' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
           $user->roles()->save(Role::findOrFail(3));
        });
    }
}
