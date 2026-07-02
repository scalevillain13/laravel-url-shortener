<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->assertTrue($user->can('view', $link));
    }

    public function test_foreign_user_cannot_view_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->assertFalse($intruder->can('view', $link));
    }

    public function test_foreign_user_cannot_delete_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->assertFalse($intruder->can('delete', $link));
    }

    public function test_owner_can_update_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->assertTrue($user->can('update', $link));
    }

    public function test_foreign_user_cannot_update_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->assertFalse($intruder->can('update', $link));
    }
}
