<?php

use App\Models\Comment;
use App\Models\Story;
use App\Models\User;

it('shows a user profile', function () {
    $user = User::factory()->create(['username' => 'goose', 'about' => 'Honk']);

    $this->get('/user/goose')->assertOk()->assertSee('goose')->assertSee('Honk');
});

it('lets the owner update their about text', function () {
    $user = User::factory()->create(['username' => 'goose']);

    $this->actingAs($user)->patch('/user', ['about' => 'From Moose Jaw'])->assertRedirect('/user/goose');

    expect($user->fresh()->about)->toBe('From Moose Jaw');
});

it('lists a user submissions and comments', function () {
    $user = User::factory()->create(['username' => 'goose']);
    Story::factory()->for($user)->create(['title' => 'Goose story']);
    Comment::factory()->for($user)->create(['body' => 'Goose comment']);

    $this->get('/user/goose/submissions')->assertOk()->assertSee('Goose story');
    $this->get('/user/goose/comments')->assertOk()->assertSee('Goose comment');
});
