<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Items with a `points` counter that always equals their number of vote rows.
 * The author's own vote is recorded on creation, so points start at 1.
 *
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
     * The vote control the viewer sees: an up arrow, an unvote link, or nothing.
     *
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
