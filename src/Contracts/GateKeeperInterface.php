<?php


namespace Chama\TeamPermission\Contracts;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface GateKeeperInterface
{

    /**
     * Check if a user is owner of a team
     *
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function isOwnerOfTeam(TeamInterface $team, Authenticatable $user): bool;

    /**
     * Check if a user is member of a team
     *
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function isMemberOfTeam(TeamInterface $team, Authenticatable $user): bool;

    /**
     * Check if a user accessing a team in a spefic route has permission to
     *
     * @param string $route
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return boolean
     */
    public function hasPermissionOnTeamTo(string $route, TeamInterface $team, Authenticatable $user): bool;

    /**
     * All teams of a type of model that user has access
     * @param TeamInterface $model
     * @param Authenticatable $user
     * @return Collection
     */
    public function allowedTeamsOf(TeamInterface $model, Authenticatable $user): Collection;
}
