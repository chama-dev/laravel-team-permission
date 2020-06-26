<?php

namespace Chama\TeamPermission\Tests\Models\Team;

use Chama\TeamPermission\Models\TeamRole;

class Role extends TeamRole
{

    protected $table = 'team_roles';

    public function team(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        // TODO: Implement team() method.
    }
}
