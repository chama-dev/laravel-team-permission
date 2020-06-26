<?php

namespace Chama\TeamPermission;

use Chama\TeamPermission\Contracts\IGateKeeperRepository;
use Chama\TeamPermission\Repositories\GateKeeperRepository;
use Illuminate\Support\ServiceProvider;

class TeamPermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/teampermission.php' => config_path('teampermission.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/teampermission.php', 'teampermission');

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
        $this->app->bind(IGateKeeperRepository::class, GateKeeperRepository::class);

    }
}
