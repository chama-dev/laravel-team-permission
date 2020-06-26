<?php


namespace Chama\TeamPermission\Repositories;


use Chama\TeamPermission\Contracts\ITeam;
use Chama\TeamPermission\Contracts\IGateKeeperRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;


class GateKeeperRepository implements IGateKeeperRepository
{

    public function isMemberOfTeam(ITeam $team, Authenticatable $user): bool
    {
        // TODO: Implement isMemberOfTeam() method.
    }

    public function hasPermissionOnTeamTo(string $route, ITeam $team, Authenticatable $user): bool
    {
        // TODO: Implement hasPermissionOnTeamTo() method.
    }

    public function allowedTeamsOf(ITeam $model, Authenticatable $user): Collection
    {
        // TODO: Implement allowedTeamsOf() method.
    }

    public function isOwnerOfTeam(ITeam $team, Authenticatable $user): bool
    {
        return ($team->getOwnerId() === $user->getKey());
    }
}
