<?php
/** @var Factory $factory */

use Chama\TeamPermission\Models\TeamRole;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(TeamRole::class, static function (Faker $faker) {
    return [];
});

$factory->state(TeamRole::class, 'spinning_instructor', static function (Faker $faker) {
    return [
        'name' => 'Instrutor de spinning',
        'enabled' => true,
        'description' => 'O instrutor de spinning poderá acessar todas as salas de spinning e a lista de todos os alunos inscritos em sua aula.',
        'permissions' => json_encode([
            'routes' => [
                'gym.rooms' => true, // Somente salas somente com aulas de spinning
                'gym.rooms.create' => false,
                'gym.rooms.post' => false,
                'gym.rooms.lessons' => true, // Somente aulas de spinning
                'gym.rooms.lessons.create' => false,
                'gym.rooms.lessons.post' => false,
                'gym.rooms.lessons.students' => true, // Alunos que participam da aula
                // ...
            ],
            'models' => [
                '\\Chama\\TeamPermission\\Tests\\Model\\Lesson' => [
                    'type_id' => 'spinning'
                ]
            ]
        ], JSON_THROW_ON_ERROR, 512)
    ];
});

$factory->state(TeamRole::class, 'chief_spinning_instructor', static function (Faker $faker) {
    return [
        'name' => 'Coordenador de instrutores de spinning',
        'enabled' => true,
        'description' => 'Terá acesso a todas as salas de spinning e a todas as turmas.',
        'permissions' => null
    ];
});
