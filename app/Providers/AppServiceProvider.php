<?php

namespace App\Providers;

use App\Enums\Province;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'story' => Story::class,
            'comment' => Comment::class,
        ]);

        Route::bind('province', fn (string $code): Province => Province::tryFrom(strtoupper($code)) ?? abort(404));

        RateLimiter::for('stories', fn (Request $request): Limit => Limit::perHour(6)->by($request->user()->id));
        RateLimiter::for('comments', fn (Request $request): Limit => Limit::perHour(30)->by($request->user()->id));
    }
}
