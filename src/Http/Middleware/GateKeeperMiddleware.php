<?php

namespace Chama\TeamPermission\Http\Middleware;

use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Chama\TeamPermission\Contracts\TeamInterface;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

abstract class GateKeeperMiddleware
{
    /**
     * You may want define routes that you can skip permissions.
     *
     * @var array
     */
    protected array $except = [];

    /**
     * @var GateKeeperInterface
     */
    private GateKeeperInterface $team;

    /**
     * Gate Keeper constructor.
     *
     * @param  GateKeeperInterface  $team
     */
    public function __construct(GateKeeperInterface $team)
    {
        $this->team = $team;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  null  $options
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, $options = null)
    {
        if (! $this->isInExceptList($route = $this->getRouteName($request))
            && ! $this->hasPermissionOnTeamTo($route, $this->getTeam($request), $request->user())) {
            throw new AuthorizationException(__('You are not allowed to access this action or resource.'));
        }

        return $next($request);
    }

    /**
     * Check if the route is in except list.
     *
     * @param  string  $route
     *
     * @return bool
     */
    protected function isInExceptList(string $route): bool
    {
        return in_array($route, $this->getExcept(), true);
    }

    /**
     * Return team to validate.
     *
     * @param  Request  $request
     * @return TeamInterface
     */
    abstract protected function getTeam(Request $request): TeamInterface;

    public function getRouteName(Request $request): string
    {
        return $request->route()->getName();
    }

    protected function hasPermissionOnTeamTo(string $route, TeamInterface $team, Authenticatable $user): bool
    {
        return $this->team->hasPermissionOnTeamTo($route, $team, $user);
    }

    /**
     * @return array
     */
    public function getExcept(): array
    {
        return $this->except;
    }
}
