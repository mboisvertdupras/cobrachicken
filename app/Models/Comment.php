<?php

namespace App\Models;

use App\Models\Concerns\Votable;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['story_id', 'user_id', 'parent_id', 'body'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, Votable;

    protected static function booted(): void
    {
        static::created(function (Comment $comment): void {
            Story::whereKey($comment->story_id)->increment('comments_count');
        });
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }
}
