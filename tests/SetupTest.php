<?php


namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\Tests\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class SetupTest
 * Check if all setup
 * @package Chama\TeamPermission\Test
 */
class SetupTest extends TestCase
{

    public function test_it_all_tables_created(): void
    {

        /*
         * Tabelas que precisam ser criadas em ordem
         * users, gyms, team_roles, team_members
         *
         */
        $this->assertDatabaseCount('users', 25);
        $this->assertDatabaseCount('gyms', 15);
    }
}
