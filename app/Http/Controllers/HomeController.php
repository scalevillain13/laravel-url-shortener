<?php

namespace App\Http\Controllers;

use App\Actions\CreateShortLinkAction;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        return view('home', [
            'recentLinks' => $request->user()
                ? Link::query()->where('user_id', $request->user()->id)->withCount('clicks')->latest()->limit(5)->get()
                : collect(),
        ]);
    }

    public function store(Request $request, CreateShortLinkAction $createLink): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()
                ->guest(route('filament.admin.auth.register'))
                ->with('prefill_original_url', (string) $request->input('original_url', ''));
        }

        $validated = $request->validate(StoreLinkRequest::baseRules());

        $link = $createLink->execute($request->user(), $validated);

        return redirect()
            ->route('home')
            ->with('success', 'Короткая ссылка создана!')
            ->with('short_url', $link->short_url);
    }
}
