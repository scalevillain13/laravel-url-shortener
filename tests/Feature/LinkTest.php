<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_codes_are_unique_and_alphanumeric(): void
    {
        $codes = collect(range(1, 20))->map(fn () => Link::generateUniqueCode());

        $this->assertSame($codes->unique()->count(), $codes->count());

        $codes->each(function (string $code) {
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{6}$/', $code);
        });
    }

    public function test_deleting_link_deletes_its_clicks(): void
    {
        $link = Link::factory()->create();

        $link->clicks()->create([
            'ip_address' => '10.0.0.1',
            'clicked_at' => now(),
        ]);

        $link->delete();

        $this->assertDatabaseCount('clicks', 0);
    }

    public function test_short_url_attribute_builds_from_code(): void
    {
        $link = Link::factory()->create(['code' => 'abc123']);

        $this->assertSame(url('/abc123'), $link->short_url);
    }
}
