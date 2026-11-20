<?php

namespace App\Models;

use App\Enums\Province;
use App\Models\Concerns\Votable;
use Database\Factories\StoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'title', 'url', 'text', 'province', 'is_political'])]
class Story extends Model
{
    /** @use HasFactory<StoryFactory> */
    use HasFactory, Votable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'province' => Province::class,
            'is_political' => 'boolean',
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Hacker News gravity: points pull a story up, age in hours pulls it down.
     */
    public function scopeRanked(Builder $query): void
    {
        $query->orderByRaw('(points - 1) / power(extract(epoch from (now() - created_at)) / 3600 + 2, 1.8) desc')
            ->orderByDesc('id');
    }

    public function scopeNewest(Builder $query): void
    {
        $query->orderByDesc('created_at')->orderByDesc('id');
    }

    public function scopeAsk(Builder $query): void
    {
        $query->whereNull('url');
    }

    public function scopeShowingPolitics(Builder $query, bool $showPolitics): void
    {
        if (! $showPolitics) {
            $query->where('is_political', false);
        }
    }

    public function scopeForListing(Builder $query, ?User $viewer): void
    {
        $query->with('user')->withVoteBy($viewer);
    }

    public function domain(): ?string
    {
        $host = $this->url ? parse_url($this->url, PHP_URL_HOST) : null;

        return $host ? preg_replace('/^www\./', '', $host) : null;
    }

    public function link(): string
    {
        return $this->url ?? route('stories.show', $this);
    }
}
