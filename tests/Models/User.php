<?php

namespace Chama\TeamPermission\Tests\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{
    // use HasTeams; Todo: Implementar a Trait HasTeams

    protected const MASTER = 'master';
    protected const REGISTERED = 'registered';

    protected $table = 'users';

    protected $guarded = [];

    protected $fillable = ['id', 'email', 'name'];

    public static function getMasterRole(): string
    {
        return self::MASTER;
    }

    public static function getRegisteredRole(): string
    {
        return self::REGISTERED;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        $name = $this->getAuthIdentifierName();

        return $this->attributes[$name];
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    public function getRememberToken()
    {
        return 'token';
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
        return 'tokenName';
    }

    public function ownedGyms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Gym::class, 'owner_id');
    }
}
