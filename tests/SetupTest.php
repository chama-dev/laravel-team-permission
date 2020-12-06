<?php

namespace Chama\TeamPermission\Tests;

/**
 * Class SetupTest
 * Check if all setup.
 */
class SetupTest extends TestCase
{
    /**
     * @test
     */
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
