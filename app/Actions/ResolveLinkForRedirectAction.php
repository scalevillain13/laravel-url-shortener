<?php

namespace App\Actions;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class ResolveLinkForRedirectAction
{
    public function execute(string $code): Link
    {
        return Cache::remember(
            Link::redirectCacheKey($code),
            now()->addHour(),
            fn (): Link => Link::query()->where('code', $code)->firstOrFail(),
        );
    }
}
