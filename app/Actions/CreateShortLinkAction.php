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
        ]);
    }
}
