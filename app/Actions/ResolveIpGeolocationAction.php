<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class ResolveIpGeolocationAction
{
    /**
     * @return array{country: ?string, city: ?string}
     */
    public function execute(string $ipAddress): array
    {
        if (! config('shortener.geoip_enabled', true)) {
            return ['country' => null, 'city' => null];
        }

        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1' || str_starts_with($ipAddress, '192.168.')) {
            return ['country' => null, 'city' => null];
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}", [
                'fields' => 'status,countryCode,city',
            ]);

            if (! $response->successful() || $response->json('status') !== 'success') {
                return ['country' => null, 'city' => null];
            }

            return [
                'country' => $response->json('countryCode'),
                'city' => $response->json('city'),
            ];
        } catch (\Throwable) {
            return ['country' => null, 'city' => null];
        }
    }
}
