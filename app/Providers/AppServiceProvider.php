<?php

namespace App\Providers;

use App\Models\Link;
use App\Policies\LinkPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('redirect', function (Request $request) {
            return Limit::perMinute((int) config('shortener.redirect_rate_limit', 60))
                ->by($request->ip());
        });

        Gate::policy(Link::class, LinkPolicy::class);
    }
}
