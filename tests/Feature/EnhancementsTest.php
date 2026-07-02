<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Сокращайте ссылки');
    }

    public function test_inactive_link_returns_410(): void
    {
        $link = Link::factory()->inactive()->create();

        $this->get('/'.$link->code)->assertStatus(410);
    }

    public function test_expired_link_returns_410(): void
    {
        $link = Link::factory()->expired()->create();

        $this->get('/'.$link->code)->assertStatus(410);
    }

    public function test_redirect_appends_utm_parameters(): void
    {
        $link = Link::factory()->create([
            'original_url' => 'https://example.com/page',
            'utm_source' => 'newsletter',
            'utm_medium' => 'email',
        ]);

        $this->get('/'.$link->code)
            ->assertRedirect('https://example.com/page?utm_source=newsletter&utm_medium=email');
    }

    public function test_bot_user_agent_is_not_recorded_when_ignore_bots_enabled(): void
    {
        config(['shortener.ignore_bots' => true]);

        $link = Link::factory()->create();

        $this->withHeader('User-Agent', 'Googlebot/2.1')
            ->get('/'.$link->code);

        $this->assertDatabaseCount('clicks', 0);
    }

    public function test_unsafe_url_scheme_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/links', [
            'original_url' => 'javascript:alert(1)',
        ]);

        $response->assertUnprocessable();
    }

    public function test_http_url_is_rejected_when_https_required(): void
    {
        config(['shortener.require_https_urls' => true]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/links', [
            'original_url' => 'http://example.com',
        ]);

        $response->assertUnprocessable();
    }

    public function test_user_cannot_view_foreign_link_via_api(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->actingAs($other, 'sanctum')
            ->getJson('/api/links/'.$link->id)
            ->assertForbidden();
    }

    public function test_api_can_create_and_list_links(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/links', ['original_url' => 'https://example.com/a'])
            ->assertCreated();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/links')
            ->assertOk()
            ->assertJsonPath('data.0.original_url', 'https://example.com/a');
    }

    public function test_api_token_can_be_issued(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/tokens', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token']);
    }
}
