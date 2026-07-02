<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => 'https://'.fake()->domainName().'/'.fake()->slug(),
            'code' => Str::random(6),
            'is_active' => true,
            'expires_at' => null,
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
