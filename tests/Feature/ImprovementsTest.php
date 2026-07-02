<?php

namespace Tests\Feature;

use App\Actions\ExportClicksToCsvAction;
use App\Http\Requests\StoreLinkRequest;
use App\Jobs\RecordClickJob;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_click_recording_is_dispatched_as_job(): void
    {
        Queue::fake();

        $link = Link::factory()->create();

        $this->get('/'.$link->code)->assertRedirect($link->original_url);

        Queue::assertPushed(RecordClickJob::class, function (RecordClickJob $job) use ($link): bool {
            return $job->linkId === $link->id
                && $job->ipAddress !== '';
        });
    }

    public function test_redirect_uses_cached_link_lookup(): void
    {
        $link = Link::factory()->create(['code' => 'cache1']);

        Cache::flush();

        $this->get('/cache1')->assertRedirect($link->original_url);

        $this->assertTrue(Cache::has(Link::redirectCacheKey('cache1')));
    }

    public function test_link_cache_is_cleared_after_update(): void
    {
        $link = Link::factory()->create(['code' => 'cache2']);

        $this->get('/cache2');
        $this->assertTrue(Cache::has(Link::redirectCacheKey('cache2')));

        $link->update(['original_url' => 'https://updated.example']);

        $this->assertFalse(Cache::has(Link::redirectCacheKey('cache2')));
    }

    public function test_owner_can_view_qr_code(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('links.qr', $link))
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml');
    }

    public function test_user_cannot_view_foreign_qr_code(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->actingAs($intruder)
            ->get(route('links.qr', $link))
            ->assertForbidden();
    }

    public function test_reserved_code_is_rejected_by_validation(): void
    {
        $validator = validator(
            ['original_url' => 'https://example.com', 'code' => 'admin'],
            (new StoreLinkRequest)->rules(),
        );

        $this->assertTrue($validator->fails());
    }

    public function test_clicks_can_be_exported_to_csv(): void
    {
        $link = Link::factory()->create();

        $link->clicks()->create([
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'clicked_at' => now(),
        ]);

        $response = app(ExportClicksToCsvAction::class)->execute($link);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }
}
