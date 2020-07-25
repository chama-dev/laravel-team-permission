<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\Contracts\GateKeeperInterface;
use Chama\TeamPermission\Models\TeamMember;
use Chama\TeamPermission\Tests\Models\Gym;
use Chama\TeamPermission\Models\TeamRole;
use Chama\TeamPermission\Tests\Models\User;


class GateKeeperTest extends TestCase
{
    private function makeSetupData($ownerName): array
    {
        /* @var User $owner */
        /* @var Gym $gym */
        $owner = factory(User::class)->state('registered')->create(['name' => $ownerName]);
        $gym = factory(Gym::class)->create(['owner_id' => $owner->getKey()]);
        $gym->roles()->save($instructorRole = factory(TeamRole::class)->state('spinning_instructor')->make());
        $gym->roles()->save($chiefInstructorRole = factory(TeamRole::class)->state('chief_spinning_instructor')->make());
        $gym->refresh();
        $this->assertCount(2, $gym->roles);

        return compact('owner', 'gym', 'instructorRole', 'chiefInstructorRole');
    }

    private function floodWithMoreMembers(TeamRole $role, int $quantity = 1): void
    {
        /* @var User $spinningInstructor */
        for ($i = 0; $i < $quantity; $i++) {
            $spinningInstructor = factory(User::class)->state('registered')->create();
            factory(TeamMember::class)->state('enabled_spinning_instructor')->create([
                'team_role_id' => $role->getKey(),
                'user_id' => $spinningInstructor->getKey(),
            ]);
        }
    }

    public function test_is_owner_of_team(): void
    {
        // Criar owner, Criar academia, Criar papeis
        /* @var User $owner */
        /* @var Gym $gym */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_is_owner_of_team'));

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        // Assertions
        $this->assertEquals(__METHOD__, $owner->getAttribute('name'));
        $this->assertCount(1, $owner->ownedGyms()->get());
        $this->assertTrue($gateKeeper->isOwnerOfTeam($gym, $owner), 'O usuário deveria ser o dono.');
        Gym::where('owner_id', '!=', $owner->getKey())->get()->each(function ($gym) use ($gateKeeper, $owner) {
            $this->assertFalse($gateKeeper->isOwnerOfTeam($gym, $owner), 'O usuário não deveria ser o dono.');
        });
    }

    public function test_should_not_be_owner_of_team(): void
    {
        /* @var User $owner */
        /* @var Gym $gym */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));

        /* @var User $user */
        $user = factory(User::class)->state('registered')->create();

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        $this->assertEquals(__METHOD__, $owner->getAttribute('name'));
        $this->assertCount(1, $owner->ownedGyms()->get());
        $this->assertTrue($gateKeeper->isOwnerOfTeam($gym, $owner), 'O usuário deveria ser o dono.');
        $this->assertFalse($gateKeeper->isOwnerOfTeam($gym, $user), 'O usuário não deveria ser o dono.');
        $this->assertCount(0, $user->ownedGyms()->get());
    }

    public function test_is_member_of_team(): void
    {
        // The owner and instructor must be
        /* @var User $owner */
        /* @var Gym $gym */
        /* @var TeamRole $instructorRole */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));

        // Flood with more members to achieve a better test
        $this->floodWithMoreMembers($instructorRole, 20);

        /* @var User $user */
        $spinningInstructor = factory(User::class)->state('registered')->create();
        factory(TeamMember::class)->state('enabled_spinning_instructor')->create([
            'team_role_id' => $instructorRole->getKey(),
            'user_id' => $spinningInstructor->getKey(),
        ]);

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        $this->assertTrue($gateKeeper->isMemberOfTeam($gym, $owner), 'The owner should be a member of this team.');
        $this->assertCount(21, $gym->teamMembers()->get());
        $this->assertTrue($gateKeeper->isMemberOfTeam($gym, $spinningInstructor), 'The instructor should be a member');

        // Time em que o instrutor não está
        $otherGym = $this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team');
        $this->assertFalse($gateKeeper->isMemberOfTeam($otherGym['gym'], $spinningInstructor), 'The instructor should not be a member');
    }

    public function test_should_not_have_permission_with_disabled_team_role_to(): void
    {
        // The owner and instructor must be
        /* @var User $owner */
        /* @var Gym $gym */
        /* @var TeamRole $instructorRole */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));

        // Flood with more members to achieve a better test
        $this->floodWithMoreMembers($instructorRole, 20);

        // Disable Team Role
        $instructorRole->setAttribute('enabled', false)->save();

        /* @var User $user */
        $spinningInstructor = factory(User::class)->state('registered')->create();
        factory(TeamMember::class)->state('enabled_spinning_instructor')->create([
            'team_role_id' => $instructorRole->getKey(),
            'user_id' => $spinningInstructor->getKey(),
        ]);

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        foreach ($this->testedRoutes() as $route => $permission) {
            // Owner
            $this->assertTrue($gateKeeper->hasPermissionOnTeamTo($route, $gym, $owner), 'The owner should had access to everything related to his gym.');
            // Instructor
            $this->assertEquals(false, $gateKeeper->hasPermissionOnTeamTo($route, $gym, $spinningInstructor));
        }
    }

    public function test_should_not_have_permission_with_disabled_team_member(): void
    {
        // The owner and instructor must be
        /* @var User $owner */
        /* @var Gym $gym */
        /* @var TeamRole $instructorRole */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));

        // Flood with more members to achieve a better test
        $this->floodWithMoreMembers($instructorRole, 20);

        /* @var User $user */
        $spinningInstructor = factory(User::class)->state('registered')->create();
        factory(TeamMember::class)->state('disabled_spinning_instructor')->create([
            'team_role_id' => $instructorRole->getKey(),
            'user_id' => $spinningInstructor->getKey(),
        ]);

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        foreach ($this->testedRoutes() as $route => $permission) {
            // Owner
            $this->assertTrue($gateKeeper->hasPermissionOnTeamTo($route, $gym, $owner), 'The owner should had access to everything related to his gym.');
            // Instructor
            $this->assertEquals(false, $gateKeeper->hasPermissionOnTeamTo($route, $gym, $spinningInstructor));
        }
    }

    public function test_should_not_have_permission_on_team_to_because_is_blocked_at_team_level(): void
    {
        // The owner and instructor must be
        /* @var User $owner */
        /* @var Gym $gym */
        /* @var TeamRole $instructorRole */
        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));

        // Flood with more members to achieve a better test
        $this->floodWithMoreMembers($instructorRole, 20);

        /* @var User $user */
        $spinningInstructor = factory(User::class)->state('registered')->create();
        factory(TeamMember::class)->state('enabled_spinning_instructor')->create([
            'team_role_id' => $instructorRole->getKey(),
            'user_id' => $spinningInstructor->getKey(),
        ]);

        /* @var GateKeeperInterface $gateKeeper */
        $gateKeeper = app(GateKeeperInterface::class);

        foreach ($this->testedRoutes() as $route => $permission) {
            // Owner
            // $this->assertTrue($gateKeeper->hasPermissionOnTeamTo($route, $gym, $owner), 'The owner should had access to everything related to his gym.');
            // Instructor
            $this->assertEquals($permission, $gateKeeper->hasPermissionOnTeamTo($route, $gym, $spinningInstructor));
        }
    }
    public function test_should_not_have_permission_on_team_to_because_is_blocked_at_member_level(): void
    {

    }

    public function test_should_have_permission_on_team_to_because_is_granted_at_member_level_even_its_blocked_at_team(): void
    {

    }

    public function test_should_not_have_permission_on_team_to_because_is_denied_at_member_level_even_its_allowed_at_team(): void
    {

    }

//    public function test_has_permission_on_team_to(): void
//    {
//        // The owner and instructor must be
//        /* @var User $owner */
//        /* @var Gym $gym */
//        /* @var TeamRole $instructorRole */
//        extract($this->makeSetupData('Chama\TeamPermission\Tests\GateKeeperTest::test_should_not_be_owner_of_team'));
//
//        // Flood with more members to achieve a better test
//        $this->floodWithMoreMembers($instructorRole, 20);
//
//        /* @var User $user */
//        $spinningInstructor = factory(User::class)->state('registered')->create();
//        factory(TeamMember::class)->state('enabled_spinning_instructor')->create([
//            'team_role_id' => $instructorRole->getKey(),
//            'user_id' => $spinningInstructor->getKey(),
//        ]);
//
//        /* @var GateKeeperInterface $gateKeeper */
//        $gateKeeper = app(GateKeeperInterface::class);
//
//        foreach ($this->testedRoutes() as $route => $permission) {
//            // Owner
//            // $this->assertTrue($gateKeeper->hasPermissionOnTeamTo($route, $gym, $owner), 'The owner should had access to everything related to his gym.');
//            // Instructor
//            $this->assertEquals($permission, $gateKeeper->hasPermissionOnTeamTo($route, $gym, $spinningInstructor));
//        }
//    }

    private function testedRoutes(): array
    {
        return [
            'gym.rooms' => true, // Somente salas somente com aulas de spinning
            'gym.rooms.create' => false,
            'gym.rooms.post' => false,
            'gym.rooms.lessons' => true, // Somente aulas de spinning
            'gym.rooms.lessons.create' => false,
            'gym.rooms.lessons.post' => false,
            'gym.rooms.lessons.students' => true, // Alunos que participam da aula
            // ...
        ];
    }

    public function test_list_allowed_teams_of(): void
    {

    }
}
