<?php


namespace Chama\TeamPermission\Contracts;


use Chama\TeamPermission\Models\TeamRole;

interface ITeam
{
    /**
     * Um time sempre terá papéis e é através deles que conectamos com
     * o usuário, caso queira usar outro nome,
     * só não implementar a interface
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\MorphMany;


    public function getOwnerId(): int;
}
