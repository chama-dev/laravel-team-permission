<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\Models\TeamRole;
use Chama\TeamPermission\Tests\Models\Gym;
use Chama\TeamPermission\Tests\Models\User;

class CreateTeamRoleTest extends TestCase
{
    public function test_it_create_a_new_team_role(): void
    {
        $user = User::where('id', self::USER_FIRST_OWNER_ID)->with('ownedGyms')->firstOrFail();
        $this->assertCount(5, $user->ownedGyms);

        /* @var Gym $gym */
        $gym = $user->ownedGyms->first();
        $gym->load('roles');

        $this->assertCount(0, $gym->roles);

        /* @var TeamRole $role */

        $gym->roles()->save($instructorRole = factory(TeamRole::class)->state('spinning_instructor')->make());
        $gym->roles()->save($chiefInstructorRole = factory(TeamRole::class)->state('chief_spinning_instructor')->make());
        $gym->refresh();
        $this->assertCount(2, $gym->roles);
        $this->assertEquals($gym->getKey(), $instructorRole->getAttribute('team_id'));
        $this->assertEquals($gym->getKey(), $chiefInstructorRole->getAttribute('team_id'));
    }
}
