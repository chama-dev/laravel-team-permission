<?php

namespace Chama\TeamPermission\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface GateKeeperInterface
{
    /**
     * Check if a user is owner of a team.
     *
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return bool
     */
    public function isOwnerOfTeam(TeamInterface $team, Authenticatable $user): bool;

    /**
     * Check if a user is member of a team.
     *
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return bool
     */
    public function isMemberOfTeam(TeamInterface $team, Authenticatable $user): bool;

    /**
     * Check if a user accessing a team in a spefic route has permission to.
     *
     * @param string $route
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return bool
     */
    public function hasPermissionOnTeamTo(string $route, TeamInterface $team, Authenticatable $user): bool;

    /**
     * All teams of a type of model that user has access.
     * @param TeamInterface $model
     * @param Authenticatable $user
     * @return Collection
     */
    public function allowedTeamsOf(TeamInterface $model, Authenticatable $user): Collection;

    /**
     * Check at team member level if the access of the user was denied
     * In a scenario that the user is in more than one role,
     * one denied access is enough to block.
     * @param string $route
     * @param Collection $memberships
     * @return bool
     */
    public function isDeniedAtMemberLevel(string $route, Collection $memberships): bool;

    /**
     * This validation is good to by pass team member level validations,
     * except if any other membership is block.
     * @param string $route
     * @param Collection $memberships
     * @return bool
     */
    public function isGrantedAtMemberLevel(string $route, Collection $memberships): bool;

    /**
     * Check if the user has permission to access the route
     * at least in one associated role.
     * @param string $route
     * @param Collection $memberships
     * @return bool
     */
    public function hasPermissionOnRoleTo(string $route, Collection $memberships): bool;

    /**
     * Return all memberships associated to a team.
     * @param TeamInterface $team
     * @param Authenticatable $user
     * @return Collection
     */
    public function getMemberships(TeamInterface $team, Authenticatable $user): Collection;
}
