<?php


namespace Chama\TeamPermission\Contracts;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface IGateKeeperRepository
{

    /**
     * Check if a user is owner of a team
     *
     * @param ITeam $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function isOwnerOfTeam(ITeam $team, Authenticatable $user): bool;

    /**
     * Check if a user is member of a team
     *
     * @param ITeam $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function isMemberOfTeam(ITeam $team, Authenticatable $user): bool;

    /**
     * Check if a user accessing a team in a spefic route has permission to
     *
     * @param string $route
     * @param ITeam $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function hasPermissionOnTeamTo(string $route, ITeam $team, Authenticatable $user): bool;

    /**
     * All teams of a type of model that user has access
     * @param ITeam $model
     * @param Authenticatable $user
     * @return Collection
     */
    public function allowedTeamsOf(ITeam $model, Authenticatable $user): Collection;
}
