<?php

namespace App\Http\Controllers;

use App\Actions\ResolveLinkForRedirectAction;
use App\Jobs\RecordClickJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(
        Request $request,
        string $code,
        ResolveLinkForRedirectAction $resolveLink,
    ): RedirectResponse {
        $link = $resolveLink->execute($code);

        RecordClickJob::dispatch(
            linkId: $link->id,
            ipAddress: $request->ip() ?? '0.0.0.0',
            userAgent: $request->userAgent(),
            clickedAt: now()->toIso8601String(),
        );

        return redirect()->away($link->original_url);
    }
}
