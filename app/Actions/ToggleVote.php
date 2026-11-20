<?php

namespace App\Actions;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ToggleVote
{
    /**
     * Converge the user's vote on the item to the wanted state. Counters move only
     * when a vote row is actually inserted or deleted, so repeats are no-ops.
     *
     * @throws AuthorizationException
     */
    public function handle(User $user, Story|Comment $votable, bool $up): void
    {
        if ($votable->user_id === $user->id) {
            throw new AuthorizationException('You cannot vote on your own submission.');
        }

        DB::transaction(function () use ($user, $votable, $up): void {
            $row = [
                'user_id' => $user->id,
                'votable_type' => $votable->getMorphClass(),
                'votable_id' => $votable->id,
            ];

            $changed = $up
                ? Vote::insertOrIgnore([...$row, 'created_at' => now()])
                : Vote::where($row)->delete();

            if ($changed === 0) {
                return;
            }

            $delta = $up ? 1 : -1;
            $votable->increment('points', $delta);
            User::whereKey($votable->user_id)->increment('karma', $delta);
        });

        $votable->refresh();
    }
}
