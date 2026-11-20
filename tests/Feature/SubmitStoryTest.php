<?php

use App\Models\Story;
use App\Models\User;

it('redirects guests to login', function () {
    $this->get('/submit')->assertRedirect('/login');
    $this->post('/submit', ['title' => 'A story', 'url' => 'https://example.ca/a'])->assertRedirect('/login');
});

it('creates a story with one point and the author vote', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/submit', [
        'title' => 'Snow day in Halifax',
        'url' => 'https://example.ca/snow',
        'province' => 'NS',
        'is_political' => '1',
    ])->assertRedirect('/newest');

    $story = Story::sole();
    expect($story->title)->toBe('Snow day in Halifax')
        ->and($story->points)->toBe(1)
        ->and($story->province->value)->toBe('NS')
        ->and($story->is_political)->toBeTrue()
        ->and($story->votes()->where('user_id', $user->id)->exists())->toBeTrue();
});

it('accepts a text post without a url', function () {
    $this->actingAs(User::factory()->create())
        ->post('/submit', ['title' => 'Ask: winter tires?', 'text' => 'Which ones?'])
        ->assertRedirect('/newest');

    expect(Story::sole()->url)->toBeNull();
});

it('requires a title', function () {
    $this->actingAs(User::factory()->create())
        ->post('/submit', ['url' => 'https://example.ca/a'])
        ->assertSessionHasErrors(['title' => 'The title field is required.']);
});

it('requires a url or text', function () {
    $this->actingAs(User::factory()->create())
        ->post('/submit', ['title' => 'Nothing here'])
        ->assertSessionHasErrors(['url' => 'Please submit a url, some text, or both.']);

    expect(Story::count())->toBe(0);
});

it('rejects an unknown province', function () {
    $this->actingAs(User::factory()->create())
        ->post('/submit', ['title' => 'A story', 'url' => 'https://example.ca/a', 'province' => 'ZZ'])
        ->assertSessionHasErrors('province');
});

it('rejects non-http urls', function () {
    $this->actingAs(User::factory()->create())
        ->post('/submit', ['title' => 'A story', 'url' => 'javascript:alert(1)'])
        ->assertSessionHasErrors('url');
});

it('limits users to six stories per hour', function () {
    $user = User::factory()->create();

    foreach (range(1, 6) as $i) {
        $this->actingAs($user)->post('/submit', ['title' => "Story {$i}", 'text' => 'Body'])->assertRedirect('/newest');
    }

    $this->actingAs($user)->post('/submit', ['title' => 'Story 7', 'text' => 'Body'])->assertTooManyRequests();
});
