<?php


namespace Chama\TeamPermission\Models;


use Illuminate\Database\Eloquent\Model;

abstract class TeamRole extends Model
{
    /**
     * Main model team
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    abstract public function team(): \Illuminate\Database\Eloquent\Relations\MorphTo;
}
