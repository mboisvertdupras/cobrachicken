<?php

use App\Enums\Province;
use App\Jobs\PostNewsStory;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * @return array{title: string, url: string, province: ?string, is_political: bool}
 */
function newsStoryAttributes(): array
{
    return [
        'title' => 'Winnipeg budget tabled',
        'url' => 'https://news.example.ca/budget',
        'province' => 'MB',
        'is_political' => true,
    ];
}

it('posts the story as the poster with the author vote', function () {
    $poster = User::factory()->create(['username' => 'MBD']);
    $this->freezeSecond();

    PostNewsStory::dispatchSync(newsStoryAttributes());

    $story = Story::sole();
    expect($story->user_id)->toBe($poster->id)
        ->and($story->title)->toBe('Winnipeg budget tabled')
        ->and($story->url)->toBe('https://news.example.ca/budget')
        ->and($story->province)->toBe(Province::Manitoba)
        ->and($story->is_political)->toBeTrue()
        ->and($story->points)->toBe(1)
        ->and($story->created_at->equalTo(now()))->toBeTrue()
        ->and($story->votes()->where('user_id', $poster->id)->count())->toBe(1);
});

it('skips a link that was posted while the job waited', function () {
    Log::spy();
    User::factory()->create(['username' => 'mbd']);
    Story::factory()->create(['url' => 'https://news.example.ca/budget?utm_source=social#top']);

    PostNewsStory::dispatchSync(newsStoryAttributes());

    expect(Story::count())->toBe(1);
    Log::shouldHaveReceived('info')->once();
});

it('does nothing when the poster account no longer exists', function () {
    Log::spy();
    config(['news.poster_username' => 'nobody_here']);

    PostNewsStory::dispatchSync(newsStoryAttributes());

    expect(Story::count())->toBe(0);
    Log::shouldHaveReceived('warning')->once();
});
