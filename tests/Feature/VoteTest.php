<?php

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;

it('counts repeated upvotes once and moves author karma with it', function () {
    $story = Story::factory()->create();
    $voter = User::factory()->create();

    $this->actingAs($voter)->postJson('/vote', ['type' => 'story', 'id' => $story->id])
        ->assertOk()->assertExactJson(['points' => 2, 'voted' => true]);
    $this->actingAs($voter)->postJson('/vote', ['type' => 'story', 'id' => $story->id])
        ->assertOk()->assertExactJson(['points' => 2, 'voted' => true]);

    expect($story->user->fresh()->karma)->toBe(2);
});

it('removes an upvote once', function () {
    $story = Story::factory()->create();
    $voter = User::factory()->create();
    $this->actingAs($voter)->postJson('/vote', ['type' => 'story', 'id' => $story->id]);

    $this->actingAs($voter)->deleteJson('/vote', ['type' => 'story', 'id' => $story->id])
        ->assertOk()->assertExactJson(['points' => 1, 'voted' => false]);
    $this->actingAs($voter)->deleteJson('/vote', ['type' => 'story', 'id' => $story->id])
        ->assertOk()->assertExactJson(['points' => 1, 'voted' => false]);

    expect($story->user->fresh()->karma)->toBe(1);
});

it('votes on comments', function () {
    $comment = Comment::factory()->create();

    $this->actingAs(User::factory()->create())->postJson('/vote', ['type' => 'comment', 'id' => $comment->id])
        ->assertOk()->assertJson(['points' => 2]);
});

it('rejects voting or unvoting your own story', function () {
    $story = Story::factory()->create();

    $this->actingAs($story->user)->postJson('/vote', ['type' => 'story', 'id' => $story->id])->assertForbidden();
    $this->actingAs($story->user)->deleteJson('/vote', ['type' => 'story', 'id' => $story->id])->assertForbidden();

    expect($story->fresh()->points)->toBe(1);
});

it('returns 401 to guests over ajax and redirects otherwise', function () {
    $story = Story::factory()->create();

    $this->postJson('/vote', ['type' => 'story', 'id' => $story->id])->assertUnauthorized();
    $this->post('/vote', ['type' => 'story', 'id' => $story->id])->assertRedirect('/login');
});

it('rejects an unknown votable type', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/vote', ['type' => 'user', 'id' => 1])
        ->assertJsonValidationErrors('type');
});

it('redirects back after a vote without javascript', function () {
    $story = Story::factory()->create();

    $this->actingAs(User::factory()->create())
        ->from('/newest')
        ->post('/vote', ['type' => 'story', 'id' => $story->id])
        ->assertRedirect('/newest');

    expect($story->fresh()->points)->toBe(2);
});
