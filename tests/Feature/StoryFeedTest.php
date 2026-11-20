<?php

use App\Enums\Province;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;

it('ranks a fresh high-point story above an older one', function () {
    $fresh = Story::factory()->create(['title' => 'Fresh story', 'points' => 10, 'created_at' => now()->subHour()]);
    $old = Story::factory()->create(['title' => 'Old story', 'points' => 40, 'created_at' => now()->subDays(2)]);

    $this->get('/')->assertOk()->assertSeeInOrder([$fresh->title, $old->title]);
});

it('lists the newest stories first', function () {
    Story::factory()->create(['title' => 'Yesterday story', 'points' => 50, 'created_at' => now()->subDay()]);
    Story::factory()->create(['title' => 'Just now story', 'created_at' => now()]);

    $this->get('/newest')->assertOk()->assertSeeInOrder(['Just now story', 'Yesterday story']);
});

it('pages through stories thirty at a time', function () {
    Story::factory()->count(31)->sequence(fn ($sequence) => ['created_at' => now()->subMinutes($sequence->index)])->create();
    $last = Story::query()->oldest()->first();

    $this->get('/newest')->assertOk()->assertDontSee($last->title)->assertSee('?p=2', false);
    $this->get('/newest?p=2')->assertOk()->assertSee($last->title)->assertSee('31.');
});

it('shows only text posts on ask', function () {
    Story::factory()->ask()->create(['title' => 'Ask: best poutine?']);
    Story::factory()->link()->create(['title' => 'A link story']);

    $this->get('/ask')->assertOk()->assertSee('Ask: best poutine?')->assertDontSee('A link story');
});

it('filters a province page by its code, case-insensitively', function () {
    Story::factory()->province(Province::Ontario)->create(['title' => 'Ontario story']);
    Story::factory()->province(Province::Quebec)->create(['title' => 'Quebec story']);

    $this->get('/province/on')->assertOk()->assertSee('Ontario story')->assertDontSee('Quebec story');
});

it('returns 404 for an unknown province code', function () {
    $this->get('/province/zz')->assertNotFound();
});

it('lists every province and territory', function () {
    $response = $this->get('/provinces')->assertOk();

    foreach (Province::cases() as $province) {
        $response->assertSee($province->label());
    }
});

it('hides political stories on the front page when the cookie is off', function () {
    Story::factory()->political()->create(['title' => 'Budget vote today']);
    Story::factory()->create(['title' => 'Hockey night']);

    $this->get('/')->assertSee('Budget vote today');
    $this->withUnencryptedCookie('show_politics', '0')->get('/')
        ->assertSee('Hockey night')
        ->assertDontSee('Budget vote today');
});

it('keeps political stories on other pages when the cookie is off', function () {
    Story::factory()->political()->create(['title' => 'Budget vote today']);

    $this->withUnencryptedCookie('show_politics', '0')->get('/newest')->assertSee('Budget vote today');
});

it('shows the story domain without www', function () {
    Story::factory()->create(['url' => 'https://www.example.ca/news/1']);

    $this->get('/')->assertSee('(example.ca)');
});

it('renders a story page with nested comments', function () {
    $story = Story::factory()->ask()->create(['text' => "First paragraph.\n\nSecond paragraph."]);
    $parent = Comment::factory()->for($story)->create(['body' => 'Top level comment']);
    Comment::factory()->replyTo($parent)->create(['body' => 'A nested reply']);

    $this->get("/item/{$story->id}")
        ->assertOk()
        ->assertSee('<p>Second paragraph.</p>', false)
        ->assertSeeInOrder(['Top level comment', 'id="children-'.$parent->id.'"', 'A nested reply'], false);
});

it('hides the vote arrow from the author and shows unvote to a voter', function () {
    $author = User::factory()->create();
    $voter = User::factory()->create();
    $story = Story::factory()->for($author)->create();
    $story->votes()->create(['user_id' => $voter->id]);

    $this->actingAs($author)->get('/')->assertDontSee('title="upvote"', false)->assertDontSee('unvote');
    $this->actingAs($voter)->get('/')->assertSee('unvote');
});

it('marks the unvote form hidden on stories the viewer has not voted on', function () {
    $viewer = User::factory()->create();
    $voted = Story::factory()->create();
    $unvoted = Story::factory()->create();
    $voted->votes()->create(['user_id' => $viewer->id]);

    $html = $this->actingAs($viewer)->get('/')->getContent();

    expect(preg_match_all('/<form[^>]*data-how="un"[^>]*>/', $html, $forms))->toBe(2);
    $hidden = collect($forms[0])->filter(fn ($form) => str_contains($form, ' hidden'));
    expect($hidden)->toHaveCount(1)
        ->and($hidden->first())->toContain($unvoted->getMorphClass().'-'.$unvoted->id);
});
