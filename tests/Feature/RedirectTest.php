<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_link_redirects_to_original_url(): void
    {
        $link = Link::factory()->create([
            'original_url' => 'https://laravel.com/docs',
        ]);

        $response = $this->get('/'.$link->code);

        $response->assertRedirect('https://laravel.com/docs');
    }

    public function test_click_is_recorded_with_ip_and_datetime(): void
    {
        $link = Link::factory()->create();

        $this->get('/'.$link->code);

        $this->assertDatabaseCount('clicks', 1);

        $click = $link->clicks()->first();

        $this->assertNotNull($click->ip_address);
        $this->assertNotNull($click->clicked_at);
        $this->assertSame($link->id, $click->link_id);
    }

    public function test_multiple_clicks_are_counted(): void
    {
        $link = Link::factory()->create();

        $this->get('/'.$link->code);
        $this->get('/'.$link->code);
        $this->get('/'.$link->code);

        $this->assertSame(3, $link->clicks()->count());
    }

    public function test_unknown_code_returns_404(): void
    {
        $response = $this->get('/unknown1');

        $response->assertNotFound();
    }
}
