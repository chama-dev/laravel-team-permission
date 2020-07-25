<?php

namespace Chama\TeamPermission;

use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Illuminate\Support\ServiceProvider;

class TeamPermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/team_permission.php' => config_path('team_permission.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/team_permission.php', 'team_permission');

        if (!class_exists('CreateTeamPermissionTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../migrations/create_team_permission_table.php.stub' => database_path("/migrations/{$timestamp}_create_team_permission_table.php"),
            ], 'migrations');
        }
    }

    public function register(): void
    {
        parent::register();
        $this->app->bind(GateKeeperInterface::class, GateKeeper::class);
    }
}
