<?php

namespace App\Actions;

use App\Models\Link;
use App\Models\User;

class CreateShortLinkAction
{
    public function execute(User $user, array $data): Link
    {
        return Link::create([
            'user_id' => $user->id,
            'original_url' => $data['original_url'],
            'code' => filled($data['code'] ?? null)
                ? $data['code']
                : Link::generateUniqueCode(),
            'is_active' => $data['is_active'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
            'utm_source' => $data['utm_source'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
        ]);
    }
}
