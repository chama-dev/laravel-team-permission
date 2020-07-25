<?php
/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(\Chama\TeamPermission\Models\TeamMember::class, static function (Faker $faker) {
    return [];
});

$factory->state(\Chama\TeamPermission\Models\TeamMember::class, 'enabled_spinning_instructor', static function (Faker $faker) {
    return [
        'team_role_id' => null,
        'user_id' => null,
        'enabled' => true,
        'permissions' => null,
    ];
});
