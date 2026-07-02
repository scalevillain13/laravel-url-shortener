<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeRedirectUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $parsed = parse_url($value);

        if ($parsed === false || ! isset($parsed['scheme'], $parsed['host'])) {
            $fail('URL должен содержать схему и домен (например, https://example.com).');

            return;
        }

        $scheme = strtolower($parsed['scheme']);

        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('Разрешены только HTTP и HTTPS ссылки.');

            return;
        }

        if (config('shortener.require_https_urls', true) && $scheme !== 'https') {
            $fail('Оригинальный URL должен использовать HTTPS.');

            return;
        }

        $host = strtolower($parsed['host']);

        if ($host === 'localhost' || $host === '127.0.0.1' || str_ends_with($host, '.local')) {
            $fail('Нельзя использовать локальные адреса в качестве целевого URL.');

            return;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ip = $host;

            if (
                ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            ) {
                $fail('Нельзя использовать приватные или зарезервированные IP-адреса.');

                return;
            }
        }
    }
}
