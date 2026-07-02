<?php

namespace App\Actions;

use App\Models\Click;
use App\Models\Link;
use Carbon\CarbonInterface;

class RecordClickAction
{
    public function execute(
        Link|int $link,
        string $ipAddress,
        ?string $userAgent,
        CarbonInterface|string $clickedAt,
    ): Click {
        $linkId = $link instanceof Link ? $link->id : $link;

        return Click::create([
            'link_id' => $linkId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'clicked_at' => $clickedAt,
        ]);
    }
}
