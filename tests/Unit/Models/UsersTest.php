<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @watch
     */
    public function a_users_ID_is_a_UUID_instead_of_an_integer()
    {
        $user = User::factory()->create();
        $this->assertFalse(is_integer($user->id));
        $this->assertEquals(36, strlen($user->id));
    }

    /**
     * @test
     * @watch
     */
    public function it_has_a_role_of_user_by_default()
    {
        $user = User::factory()->create();
        $this->assertEquals('user', $user->role);
    }
}
