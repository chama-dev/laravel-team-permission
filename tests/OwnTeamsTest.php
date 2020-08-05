<?php

namespace Chama\TeamPermission\Tests;

use Chama\TeamPermission\Tests\Models\User;

class OwnTeamsTest extends TestCase
{
    public function test_it_own_a_team(): void
    {
        $firstOwner = User::where('id', self::USER_FIRST_OWNER_ID)->withCount('ownedGyms')->firstOrFail();
        $secondOwner = User::where('id', self::USER_SECOND_OWNER_ID)->withCount('ownedGyms')->firstOrFail();

        $this->assertEquals(5, $firstOwner->owned_gyms_count);
        $this->assertEquals(10, $secondOwner->owned_gyms_count);
    }
}
