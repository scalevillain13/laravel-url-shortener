<?php

namespace App\Actions;

use App\Models\Click;
use App\Models\Link;
use App\Support\BotDetector;
use Carbon\CarbonInterface;

class RecordClickAction
{
    public function __construct(
        private ResolveIpGeolocationAction $geolocation,
    ) {}

    public function execute(
        Link|int $link,
        string $ipAddress,
        ?string $userAgent,
        CarbonInterface|string $clickedAt,
    ): ?Click {
        $isBot = BotDetector::isBot($userAgent);

        if ($isBot && config('shortener.ignore_bots', true)) {
            return null;
        }

        $linkId = $link instanceof Link ? $link->id : $link;

        $geo = $this->geolocation->execute($ipAddress);

        return Click::create([
            'link_id' => $linkId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'country' => $geo['country'],
            'city' => $geo['city'],
            'is_bot' => $isBot,
            'clicked_at' => $clickedAt,
        ]);
    }
}
