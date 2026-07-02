<?php

namespace App\Actions;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class ResolveLinkForRedirectAction
{
    public function execute(string $code): Link
    {
        $linkId = Cache::remember(
            Link::redirectCacheKey($code),
            now()->addHour(),
            fn (): int => Link::query()->where('code', $code)->firstOrFail()->id,
        );

        return Link::query()->findOrFail($linkId);
    }
}
