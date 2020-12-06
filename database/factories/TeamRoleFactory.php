<?php

namespace Chama\TeamPermission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Chama\TeamPermission\Models\TeamRole;

class TeamRoleFactory extends Factory
{
    protected $model = TeamRole::class;

    public function definition(): array
    {
        return [
            'name' => 'Nome do papel',
            'enabled' => false,
            'description' => 'Descrição do papel',
            'permissions' => null
        ];
    }

    public function spinningInstructor(): TeamRoleFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Instrutor de spinning',
                'enabled' => true,
                'description' => 'O instrutor de spinning poderá acessar todas as salas de spinning e a lista de todos os alunos inscritos em sua aula.',
                'permissions' => [
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
                ]
            ];
        });
    }

    public function chiefSpinningInstructor(): TeamRoleFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Coordenador de instrutores de spinning',
                'enabled' => true,
                'description' => 'Terá acesso a todas as salas de spinning e a todas as turmas.',
                'permissions' => null
            ];
        });
    }
}
