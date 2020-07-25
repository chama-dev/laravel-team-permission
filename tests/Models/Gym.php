<?php


namespace Chama\TeamPermission\Tests\Models;


use Chama\TeamPermission\Contracts\TeamInterface;
use Chama\TeamPermission\Models\TeamMember;
use Chama\TeamPermission\Models\TeamRole;
use Chama\TeamPermission\Traits\TeamTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model implements TeamInterface
{
    use TeamTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gyms';

    protected $fillable = ['name', 'owner_id'];

    public function roles(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(TeamRole::class, 'team');
    }

    public function getOwnerId(): int
    {
        return $this->getAttribute('owner_id');
    }

}
