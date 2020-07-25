<?php


namespace Chama\TeamPermission;


use Chama\TeamPermission\Contracts\TeamInterface;
use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;


class GateKeeper implements GateKeeperInterface
{

    public function isMemberOfTeam(TeamInterface $team, Authenticatable $user): bool
    {
        if (!$this->isOwnerOfTeam($team, $user)) {
            // O usário pode estar associado a mais de um papel no time, por isso ">"
            return ($team->teamMembers()->where('user_id', $user->getKey())->count() > 0);
        }

        return true;
    }

    public function hasPermissionOnTeamTo(string $route, TeamInterface $team, Authenticatable $user): bool
    {
//        if (!$this->isMemberOfTeam($team, $user)) {
//            return false;
//        }
        if (!$this->isOwnerOfTeam($team, $user)) {
            /*
             * Agora eu lembrei, retorno o link do usuário com o time e o papel
             * A verificação aqui parte da seguinte hierarquia
             * 1. enabled at Role level disable everyone at this role
             * 2. enabled at Member level disable the user
             * 3. permissions at team_members
             * 4. permissions at team_roles
             * Todo: implement Grant and deny rules at Team Member level
             */
            $membership = $team->teamMembers()
                // Team Role Enabled
                ->whereHas('teamRole', static function ($query) {
                    return $query->where('team_roles.enabled', true);
                })->with(['teamRole' => static function ($query) {
                    return $query->where('team_roles.enabled', true);
                }])
                // Team Member enabled
                ->where('team_members.enabled', true)
                ->where('user_id', $user->getKey())->get();

            if ($membership->isEmpty()) {
                return false;
            }

            dd($route, $membership->toArray());
            // ->whereRaw("json_extract(permissions, '$.\"routes\".\"{$route}\"') = true")
            return false;
        }


        return true;
    }

    public function allowedTeamsOf(TeamInterface $model, Authenticatable $user): Collection
    {
        // TODO: Implement allowedTeamsOf() method.
    }

    public function isOwnerOfTeam(TeamInterface $team, Authenticatable $user): bool
    {
        return ($team->getOwnerId() === $user->getKey());
    }
}
