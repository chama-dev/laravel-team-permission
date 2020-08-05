<?php

namespace Chama\TeamPermission;

use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Chama\TeamPermission\Contracts\TeamInterface;
use Chama\TeamPermission\Models\TeamMember;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

class GateKeeper implements GateKeeperInterface
{
    public function isMemberOfTeam(TeamInterface $team, Authenticatable $user): bool
    {
        if (! $this->isOwnerOfTeam($team, $user)) {
            // O usário pode estar associado a mais de um papel no time, por isso ">"
            return $team->teamMembers()->where('user_id', $user->getKey())->count() > 0;
        }

        return true;
    }

    public function hasPermissionOnTeamTo(string $route, TeamInterface $team, Authenticatable $user): bool
    {
        // 1. If its owner don't even care
        if ($this->isOwnerOfTeam($team, $user)) {
            return true;
        }

        // If is not let's see if has any membership
        $memberships = $this->getMemberships($team, $user);

        // If it's not a member, just block
        if ($memberships->isEmpty()) {
            return false;
        }

        // So the user is a member, but let's check if someone denied access at this route in any role
        if ($this->isDeniedAtMemberLevel($route, $memberships)) {
            return false;
        }

        // Lets check if someone give a special access at this route
        if ($this->isGrantedAtMemberLevel($route, $memberships)) {
            return true;
        }

        // Finally we get at role level, the must had at least one role passing
        if (! $this->hasPermissionOnRoleTo($route, $memberships)) {
            return false;
        }

        return true;
    }

    public function isDeniedAtMemberLevel(string $route, Collection $memberships): bool
    {
        return $memberships->whereNotNull('permissions.denied')->filter(static function (TeamMember $membership) use ($route) {
            return in_array($route, $membership->getAttribute('permissions')['denied'], true);
        })->isNotEmpty();
    }

    public function isGrantedAtMemberLevel(string $route, Collection $memberships): bool
    {
        return $memberships->whereNotNull('permissions.granted')->filter(static function (TeamMember $membership) use ($route) {
            return in_array($route, $membership->getAttribute('permissions')['granted'], true);
        })->isNotEmpty();
    }

    public function hasPermissionOnRoleTo(string $route, Collection $memberships): bool
    {
        return $memberships->whereNotNull('teamRole.permissions.routes')->filter(static function (TeamMember $membership) use ($route) {
            return array_key_exists($route, $membership->teamRole->getAttribute('permissions')['routes']) && $membership->teamRole->getAttribute('permissions')['routes'][$route];
        })->isNotEmpty();
    }

    public function allowedTeamsOf(TeamInterface $model, Authenticatable $user): Collection
    {
        // TODO: Implement allowedTeamsOf() method.
    }

    public function isOwnerOfTeam(TeamInterface $team, Authenticatable $user): bool
    {
        return $team->getOwnerId() === $user->getKey();
    }

    public function getMemberships(TeamInterface $team, Authenticatable $user): Collection
    {
        return $team->teamMembers()
            // Team Role Enabled
            ->whereHas('teamRole', static function ($query) {
                return $query->where('team_roles.enabled', true);
            })->with(['teamRole' => static function ($query) {
                return $query->where('team_roles.enabled', true);
            }])
            // Team Member enabled
            ->where('team_members.enabled', true)
            ->where('user_id', $user->getKey())->get();
    }
}
