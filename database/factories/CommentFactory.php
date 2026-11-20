<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'story_id' => Story::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'body' => fake()->paragraph(),
        ];
    }

    public function replyTo(Comment $parent): static
    {
        return $this->state([
            'story_id' => $parent->story_id,
            'parent_id' => $parent->id,
        ]);
    }
}
