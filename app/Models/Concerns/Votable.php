<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property bool|null $voted
 */
trait Votable
{
    public static function bootVotable(): void
    {
        static::created(function (self $votable): void {
            $votable->votes()->create(['user_id' => $votable->user_id]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function scopeWithVoteBy(Builder $query, ?User $user): void
    {
        if ($user) {
            $query->withExists(['votes as voted' => fn (Builder $votes) => $votes->where('user_id', $user->id)]);
        }
    }

    /**
     * @return 'up'|'un'|null
     */
    public function voteControlFor(?User $viewer): ?string
    {
        return match (true) {
            $viewer === null => 'up',
            $viewer->id === $this->user_id => null,
            (bool) $this->voted => 'un',
            default => 'up',
        };
    }
}
