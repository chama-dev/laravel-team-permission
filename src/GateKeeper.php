<?php


namespace Chama\TeamPermission;


use Chama\TeamPermission\Contracts\TeamInterface;
use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Chama\TeamPermission\Models\TeamMember;
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
            $memberships = $team->teamMembers()
                // Team Role Enabled
                ->whereHas('teamRole', static function ($query) {
                    return $query->where('team_roles.enabled', true);
                })->with(['teamRole' => static function ($query) {
                    return $query->where('team_roles.enabled', true);
                }])
                // Team Member enabled
                ->where('team_members.enabled', true)
                ->where('user_id', $user->getKey())->get();

            // Check if the user is a member
            if ($memberships->isEmpty()) {
                return false;
            }

            // Check at TeamMember Level , como o usuário pode estar em vários papéis
            // Se a rota estiver definida neste nível valida aqui
            // Todo: Transformar em um reduce, basta um falso na lista para bloquear tudo
            // O usuário está bloqueado no nível de membro
            // Todo: ->isGrantedAtMemberLevel() : bool

            if ($memberships->whereNotNull("permissions.denied")->filter(static function (TeamMember $membership) use ($route) {
                return (in_array($route, $membership->getAttribute('permissions')['denied'], true));
            })->isNotEmpty()) {
                return false;
            }

            // Se nenhum team member tiver bloqueado anteriormente e tiver permissão no nível de membro, já passa
            // Todo: ->isDeniedAtMemberLevel() : bool
            if ($memberships->whereNotNull("permissions.granted")->filter(static function (TeamMember $membership) use ($route) {
                return (in_array($route, $membership->getAttribute('permissions')['granted'], true));
            })->isNotEmpty()) {
                return true;
            }

            //Team Role: Preciso de pelo menos um papel com permissão para passar
            // Todo: hasPermissionOnRoleTo
            if ($memberships->whereNotNull('teamRole.permissions.routes')->filter(static function (TeamMember $membership) use ($route) {
                return (array_key_exists($route, $membership->teamRole->getAttribute('permissions')['routes']) && $membership->teamRole->getAttribute('permissions')['routes'][$route]);
            })->isNotEmpty()) {
                return true;
            }
            // Algum limbo que ainda não previ. Depois posso reordenar tudo para limpar o código
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
