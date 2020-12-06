<?php

namespace Chama\TeamPermission\Database\Factories;

use Chama\TeamPermission\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'role' => null,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }

    public function registered(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::getRegisteredRole(),
            ];
        });
    }

    public function master(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => User::getMasterRole(),
            ];
        });
    }
}
