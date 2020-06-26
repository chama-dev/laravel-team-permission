<?php


namespace Chama\TeamPermission\Tests\Models;


use Chama\TeamPermission\Contracts\ITeam;
use Chama\TeamPermission\Models\TeamRole;
use Chama\TeamPermission\Tests\Models\GymRole;
use Chama\TeamPermission\Tests\Models\Team\Role;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model implements ITeam
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gyms';

    protected $fillable = ['name', 'owner_id'];

    public function roles(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Role::class, 'team');
    }

    public function getOwnerId(): int
    {
        return $this->getAttribute('owner_id');
    }
}
