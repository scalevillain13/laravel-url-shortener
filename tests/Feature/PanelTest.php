<?php

namespace Tests\Feature;

use App\Filament\Resources\LinkResource\Pages\CreateLink;
use App\Filament\Resources\LinkResource\Pages\EditLink;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/links')->assertRedirect('/admin/login');
    }

    public function test_registration_page_is_available(): void
    {
        $this->get('/admin/register')->assertOk();
    }

    public function test_authenticated_user_can_see_links_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/links')->assertOk();
    }

    public function test_user_can_create_link_and_code_is_generated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateLink::class)
            ->fillForm([
                'original_url' => 'https://example.com/page',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $link = Link::first();

        $this->assertNotNull($link);
        $this->assertSame($user->id, $link->user_id);
        $this->assertSame('https://example.com/page', $link->original_url);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{6}$/', $link->code);
    }

    public function test_user_sees_only_own_links(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownLink = Link::factory()->for($owner)->create();
        $foreignLink = Link::factory()->for($other)->create();

        $this->actingAs($owner)
            ->get('/admin/links')
            ->assertOk()
            ->assertSee($ownLink->code)
            ->assertDontSee($foreignLink->code);
    }

    public function test_user_can_edit_own_link_in_panel(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create([
            'original_url' => 'https://example.com/old',
        ]);

        Livewire::actingAs($user)
            ->test(EditLink::class, ['record' => $link->getRouteKey()])
            ->fillForm([
                'original_url' => 'https://example.com/new',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('https://example.com/new', $link->fresh()->original_url);
    }
}
