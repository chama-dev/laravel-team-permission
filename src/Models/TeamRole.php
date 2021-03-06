<?php

namespace Chama\TeamPermission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    use HasFactory;

    protected $table = 'team_roles';

    protected $casts = [
        'enabled' => 'boolean',
        'permissions' => 'json',
    ];

    /**
     * Main model team.
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function team(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
    }
}
