<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateShortLinkAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $links = Link::query()
            ->where('user_id', $request->user()->id)
            ->withCount('clicks')
            ->latest()
            ->paginate(15);

        return response()->json($links);
    }

    public function store(Request $request, CreateShortLinkAction $createLink): JsonResponse
    {
        $validated = $request->validate(StoreLinkRequest::baseRules());

        $link = $createLink->execute($request->user(), $validated);
        $link->loadCount('clicks');

        return response()->json($link, 201);
    }

    public function show(Request $request, Link $link): JsonResponse
    {
        $this->authorize('view', $link);

        $link->loadCount('clicks');
        $link->load(['clicks' => fn ($q) => $q->latest('clicked_at')->limit(50)]);

        return response()->json([
            'link' => $link,
            'stats' => [
                'total_clicks' => $link->clicks_count,
                'clicks_today' => $link->clicks()->whereDate('clicked_at', today())->count(),
            ],
        ]);
    }

    public function update(UpdateLinkRequest $request, Link $link): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('code', $validated) && blank($validated['code'])) {
            unset($validated['code']);
        }

        $link->update($validated);
        $link->loadCount('clicks');

        return response()->json($link);
    }

    public function destroy(Request $request, Link $link): JsonResponse
    {
        $this->authorize('delete', $link);

        $link->delete();

        return response()->json(['message' => 'Ссылка удалена.']);
    }
}
