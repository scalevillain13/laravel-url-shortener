<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'code',
    ];

    protected static function booted(): void
    {
        static::saved(function (Link $link): void {
            Cache::forget(self::redirectCacheKey($link->code));
        });

        static::deleted(function (Link $link): void {
            Cache::forget(self::redirectCacheKey($link->code));
        });
    }

    /**
     * @return list<string>
     */
    public static function reservedCodes(): array
    {
        return [
            'admin',
            'api',
            'filament',
            'links',
            'livewire',
            'login',
            'register',
        ];
    }

    public static function redirectCacheKey(string $code): string
    {
        return 'link:redirect:'.$code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }

    public function getShortUrlAttribute(): string
    {
        return url('/'.$this->code);
    }

    public static function generateUniqueCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
