<?php
/** @var Factory $factory */

use Chama\TeamPermission\Tests\Models\Gym;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Gym::class, static function (Faker $faker) {
    return [
        'name' => $faker->company,
        'owner_id' => null
    ];
});
