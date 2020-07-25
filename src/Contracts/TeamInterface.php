<?php


namespace Chama\TeamPermission\Contracts;


use Chama\TeamPermission\Models\TeamRole;
use Illuminate\Contracts\Auth\Authenticatable;

interface TeamInterface
{
    /**
     * Um time sempre terá papéis e é através deles que conectamos com
     * o usuário, caso queira usar outro nome,
     * só não implementar a interface
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\MorphMany;


    public function getOwnerId(): int;

    /**
     * Transforma um usuário em um membro da equipe
     * @param Authenticatable $user
     * @return $this
     */
    public function addTeamMember(Authenticatable $user): TeamInterface;

    /**
     * Lets use a explicit name, feel free to change at your model
     * @return mixed
     */
    public function teamMembers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough;
}
