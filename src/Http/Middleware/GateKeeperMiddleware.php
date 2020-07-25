<?php


namespace Chama\TeamPermission\Http\Middleware;

use Closure;
use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Illuminate\Auth\Access\AuthorizationException;

abstract class GateKeeperMiddleware
{
    /**
     * You may want define routes that you can skip permissions
     *
     * @var array
     */
    protected $skip = [
        // manager.dashboard
    ];

    /**
     * @var GateKeeperInterface
     */
    private $team;

    /**
     * Gate Keeper constructor.
     *
     * @param GateKeeperInterface $team
     */
    public function __construct(GateKeeperInterface $team)
    {
        $this->team = $team;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param null $options
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, $options = null)
    {
        if (!$this->isInExceptList($request->route()->getName())
            && !$this->team->hasPermissionOnTeamTo(
                $request->route()->getName(),
                $request->route('venue'),
                auth()->user())
        ) {
            throw new AuthorizationException(__('You are not allowed to access this action or resource.'));
        }

        return $next($request);
    }

    /**
     * Verifica se a rota está na lista de exceção
     *
     * @param string $route
     *
     * @return bool
     */
    private function isInExceptList(string $route): bool
    {
        return in_array($route, $this->except);
    }
}
