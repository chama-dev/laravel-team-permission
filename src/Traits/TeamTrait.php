<?php


namespace Chama\TeamPermission\Traits;


use Chama\TeamPermission\Contracts\TeamInterface;
use Chama\TeamPermission\Models\TeamMember;
use Chama\TeamPermission\Models\TeamRole;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Query\Builder;

trait TeamTrait
{
    /**
     * Transforma um usuário em um membro da equipe
     * @param Authenticatable $user
     * @return TeamInterface|TeamTrait
     */
    public function addTeamMember(Authenticatable $user): TeamInterface
    {
        return $this;
    }

    /**
     * Lets use a explicit name, feel free to change at your model
     * @return mixed
     */
    public function teamMembers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->newHasManyThrough(
            $this->newRelatedInstance(TeamMember::class)->newQuery()->where('team_roles.team_type', $this->getMorphClass()),
            $this,
            $through = new TeamRole,
            'team_id',
            'team_role_id',
            $this->getKeyName(),
            $through->getKeyName()
        );
    }
}
