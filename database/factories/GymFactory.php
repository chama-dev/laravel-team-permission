<?php

namespace Chama\TeamPermission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Chama\TeamPermission\Tests\Models\Gym;

class GymFactory extends Factory
{
    protected $model = Gym::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'owner_id' => null
        ];
    }
}
