<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\Contracts\IGateKeeperRepository;
use Chama\TeamPermission\Tests\Models\Gym;
use Chama\TeamPermission\Tests\Models\User;


class GateKeeperRepositoryTest extends TestCase
{
    private function setUpData(){
        
    }

    public function test_is_owner_of_team(): void
    {
        // Criar owner, Criar academia, Criar papeis
        /* @var User $owner */
        $owner = factory(User::class)->state('registered')->create(['name' => 'Chama\TeamPermission\Tests\GateKeeperRepositoryTest::test_is_owner_of_team']);
        $gym = factory(Gym::class)->create(['owner_id' => $owner->getKey()]);

        /* @var IGateKeeperRepository $gateKeeper */
        $gateKeeper = app(IGateKeeperRepository::class);

        // Assertions
        $this->assertEquals(__METHOD__, $owner->getAttribute('name'));
        $this->assertCount(1, $owner->ownedGyms()->get());
        $this->assertTrue($gateKeeper->isOwnerOfTeam($gym, $owner), 'O usuário deveria ser o dono.');
        Gym::where('owner_id', '!=', $owner->getKey())->get()->each(function ($gym) use ($gateKeeper, $owner) {
            $this->assertFalse($gateKeeper->isOwnerOfTeam($gym, $owner), 'O usuário não deveria ser o dono.');
        });
    }

    public function test_is_member_of_team()
    {
        // Criar owner, Criar academia, Criar papeis, Criar instrutor, Associar instrutor ao papel

    }

    public function test_has_permision_on_team_to()
    {

    }

    public function testAllowedTeamsOf()
    {

    }


}
