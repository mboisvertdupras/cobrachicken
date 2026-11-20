<?php

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;

it('adds a top-level comment and returns its partial over ajax', function () {
    $story = Story::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post("/item/{$story->id}/comments", ['body' => 'Go Riders <b>go</b>'], ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertCreated()
        ->assertSee('Go Riders &lt;b&gt;go&lt;/b&gt;', false)
        ->assertSee($user->username)
        ->assertDontSee('<html', false);

    $comment = Comment::sole();
    expect($story->fresh()->comments_count)->toBe(1)
        ->and($comment->points)->toBe(1)
        ->and($comment->parent_id)->toBeNull();
});

it('adds a reply to a comment', function () {
    $parent = Comment::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post("/item/{$parent->story_id}/comments", ['body' => 'A reply', 'parent_id' => $parent->id])
        ->assertRedirect("/item/{$parent->story_id}");

    expect(Comment::where('parent_id', $parent->id)->sole()->body)->toBe('A reply')
        ->and($parent->story->fresh()->comments_count)->toBe(2);
});

it('rejects a parent comment from another story', function () {
    $story = Story::factory()->create();
    $otherComment = Comment::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson("/item/{$story->id}/comments", ['body' => 'A reply', 'parent_id' => $otherComment->id])
        ->assertJsonValidationErrors('parent_id');
});

it('requires a body', function () {
    $story = Story::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson("/item/{$story->id}/comments", ['body' => ''])
        ->assertJsonValidationErrors(['body' => 'The body field is required.']);
});

it('redirects guests to login', function () {
    $story = Story::factory()->create();

    $this->post("/item/{$story->id}/comments", ['body' => 'Hi'])->assertRedirect('/login');
});

it('lists the newest comments with their story', function () {
    $comment = Comment::factory()->create(['body' => 'Latest thoughts']);

    $this->get('/comments')->assertOk()->assertSee('Latest thoughts')->assertSee($comment->story->title);
});
