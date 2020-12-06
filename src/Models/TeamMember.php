<?php

namespace Chama\TeamPermission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array permissions
 * @property TeamRole teamRole
 */
class TeamMember extends Model
{
    use HasFactory;
    protected $table = 'team_members';

    protected $casts = [
        'enabled' => 'boolean',
        'permissions' => 'json',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function teamRole(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeamRole::class, 'team_role_id');
    }
}
