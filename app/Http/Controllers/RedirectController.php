<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $link = Link::where('code', $code)->firstOrFail();

        $link->clicks()->create([
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'clicked_at' => now(),
        ]);

        return redirect()->away($link->original_url);
    }
}
