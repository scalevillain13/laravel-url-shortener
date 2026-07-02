<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    protected $fillable = [
        'link_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'is_bot',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
            'is_bot' => 'boolean',
        ];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
