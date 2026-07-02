<?php

namespace App\Support;

class BotDetector
{
    /**
     * @var list<string>
     */
    private const PATTERNS = [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'facebookexternalhit',
        'whatsapp',
        'telegrambot',
        'preview',
        'headless',
        'curl/',
        'wget',
        'python-requests',
        'go-http-client',
    ];

    public static function isBot(?string $userAgent): bool
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return false;
        }

        $ua = strtolower($userAgent);

        foreach (self::PATTERNS as $pattern) {
            if (str_contains($ua, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
