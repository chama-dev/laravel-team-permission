<?php

namespace Chama\TeamPermission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Chama\TeamPermission\Models\TeamMember;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'team_role_id' => null,
            'user_id' => null,
            'enabled' => false,
            'permissions' => null,
        ];
    }

    public function enabledSpinningInstructor(): TeamMemberFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => true,
            ];
        });
    }

    public function disabledSpinningInstructor(): TeamMemberFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'enabled' => false,
            ];
        });
    }
}
