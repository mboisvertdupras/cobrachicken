<?php

use App\Enums\Province;
use App\Jobs\PostNewsStory;
use App\Models\Story;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * @param  list<array{0: string, 1: string, 2?: Carbon|null}>  $items  title, link, and publication time (default: an hour ago)
 */
function rssFeed(array $items): string
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel><title>Test</title>';

    foreach ($items as $item) {
        $date = array_key_exists(2, $item) ? $item[2] : now()->subHour();
        $xml .= '<item><title>'.$item[0].'</title><link>'.htmlspecialchars($item[1], ENT_XML1).'</link>'
            .($date ? '<pubDate>'.$date->toRfc2822String().'</pubDate>' : '').'</item>';
    }

    return $xml.'</channel></rss>';
}

/**
 * @param  array<string, array{name?: string, province?: ?Province, politics?: bool, body?: string, status?: int}>  $feeds  keyed by url
 */
function fakeFeeds(array $feeds): void
{
    config(['news.feeds' => collect($feeds)->map(fn (array $feed, string $url) => [
        'name' => $feed['name'] ?? $url,
        'url' => $url,
        'province' => $feed['province'] ?? null,
        'politics' => $feed['politics'] ?? false,
    ])->values()->all()]);

    Http::preventStrayRequests();
    Http::fake(collect($feeds)->mapWithKeys(fn (array $feed, string $url) => [
        $url => Http::response($feed['body'] ?? '', $feed['status'] ?? 200),
    ])->all());
}

beforeEach(function () {
    $this->poster = User::factory()->create(['username' => 'mbd']);
});

/*
 * The command queues a PostNewsStory job per story. The tests run on the sync queue,
 * which ignores the delays and runs each job at once, so most tests below check the
 * stories that end up on the site. The tests that fake the queue check the delays.
 */

/**
 * @return list<int> the delay of each queued PostNewsStory job, in seconds from now
 */
function queuedDelays(): array
{
    return Queue::pushed(PostNewsStory::class)
        ->map(fn (PostNewsStory $job) => (int) now()->diffInSeconds($job->delay))
        ->values()
        ->all();
}

it('posts feed stories as the poster with the author vote', function () {
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([
        ['Snow &amp; ice in Halifax &#8217;s harbour', 'https://news.example.ca/snow'],
    ])]]);

    $this->artisan('news:fetch')->assertSuccessful();

    $story = Story::sole();
    expect($story->user_id)->toBe($this->poster->id)
        ->and($story->title)->toBe('Snow & ice in Halifax ’s harbour')
        ->and($story->url)->toBe('https://news.example.ca/snow')
        ->and($story->text)->toBeNull()
        ->and($story->points)->toBe(1)
        ->and($story->votes()->where('user_id', $this->poster->id)->count())->toBe(1)
        ->and($this->poster->fresh()->karma)->toBe(1);

    Http::assertSent(fn ($request) => $request->hasHeader('User-Agent', config('news.user_agent')));
});

it('finds the poster case-insensitively', function () {
    config(['news.poster_username' => 'MBD']);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([['A story', 'https://news.example.ca/a']])]]);

    $this->artisan('news:fetch')->assertSuccessful();

    expect(Story::sole()->user_id)->toBe($this->poster->id);
});

it('fails clearly when the poster account does not exist', function () {
    config(['news.poster_username' => 'nobody_here']);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([['A story', 'https://news.example.ca/a']])]]);

    $this->artisan('news:fetch')
        ->expectsOutputToContain('The news poster account "nobody_here" does not exist.')
        ->assertFailed();

    expect(Story::count())->toBe(0)->and(User::count())->toBe(1);
    Http::assertNothingSent();
});

it('tags province and politics from the feed config and merges tags for shared links', function () {
    fakeFeeds([
        'https://news.example.ca/politics' => ['politics' => true, 'body' => rssFeed([
            ['Budget tabled', 'https://news.example.ca/budget'],
        ])],
        'https://news.example.ca/manitoba' => ['province' => Province::Manitoba, 'body' => rssFeed([
            ['Winnipeg snow', 'https://news.example.ca/snow'],
            ['Budget tabled', 'https://news.example.ca/budget?utm_source=rss'],
        ])],
        'https://news.example.ca/top' => ['body' => rssFeed([['World cup', 'https://news.example.ca/cup']])],
    ]);

    $this->artisan('news:fetch', ['--limit' => 10])->assertSuccessful();

    $stories = Story::all()->keyBy('url');
    expect($stories)->toHaveCount(3)
        ->and($stories['https://news.example.ca/budget']->is_political)->toBeTrue()
        ->and($stories['https://news.example.ca/budget']->province)->toBe(Province::Manitoba)
        ->and($stories['https://news.example.ca/snow']->is_political)->toBeFalse()
        ->and($stories['https://news.example.ca/snow']->province)->toBe(Province::Manitoba)
        ->and($stories['https://news.example.ca/cup']->province)->toBeNull()
        ->and($stories['https://news.example.ca/cup']->is_political)->toBeFalse();
});

it('leaves the province empty when feeds disagree on it', function () {
    fakeFeeds([
        'https://news.example.ca/ottawa' => ['province' => Province::Ontario, 'body' => rssFeed([
            ['Quebec oath vote', 'https://news.example.ca/montreal/oath'],
            ['Ottawa transit', 'https://news.example.ca/ottawa/transit'],
        ])],
        'https://news.example.ca/montreal' => ['province' => Province::Quebec, 'body' => rssFeed([
            ['Quebec oath vote', 'https://news.example.ca/montreal/oath'],
        ])],
        'https://news.example.ca/toronto' => ['province' => Province::Ontario, 'body' => rssFeed([
            ['Ottawa transit', 'https://news.example.ca/ottawa/transit'],
        ])],
    ]);

    $this->artisan('news:fetch', ['--limit' => 10])->assertSuccessful();

    $stories = Story::all()->keyBy('url');
    expect($stories['https://news.example.ca/montreal/oath']->province)->toBeNull()
        ->and($stories['https://news.example.ca/ottawa/transit']->province)->toBe(Province::Ontario);
});

it('accepts a province code string in the feed config', function () {
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([['Ferry delays', 'https://news.example.ca/ferry']])]]);
    config(['news.feeds.0.province' => 'BC']);

    $this->artisan('news:fetch')->assertSuccessful();

    expect(Story::sole()->province)->toBe(Province::BritishColumbia);
});

it('skips links that are already stories, ignoring utm parameters and fragments', function () {
    Story::factory()->create(['title' => 'Existing', 'url' => 'https://NEWS.example.ca/existing?id=7#comments']);
    Story::factory()->create(['title' => 'Tracked', 'url' => 'https://news.example.ca/tracked?utm_medium=social']);

    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([
        ['Already here', 'https://news.example.ca/existing?utm_source=rss&id=7'],
        ['Already here too', 'https://news.example.ca/tracked#top'],
        ['Different query', 'https://news.example.ca/existing?id=8'],
        ['Fresh', 'https://news.example.ca/fresh?utm_campaign=x#a'],
        ['Fresh again', 'https://news.example.ca/fresh?utm_source=y'],
    ])]]);

    $this->artisan('news:fetch', ['--limit' => 10, '--per-feed' => 10])->assertSuccessful();

    expect(Story::where('user_id', $this->poster->id)->pluck('url')->sort()->values()->all())->toBe([
        'https://news.example.ca/existing?id=8',
        'https://news.example.ca/fresh',
    ]);
});

it('does not repost on a second run', function () {
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([
        ['One', 'https://www.cbc.ca/news/one?cmp=rss'],
        ['Two', 'https://news.example.ca/two'],
    ])]]);

    $this->artisan('news:fetch')->assertSuccessful();
    $this->artisan('news:fetch')->expectsOutputToContain('Queued 0 new stories')->assertSuccessful();

    expect(Story::pluck('url')->sort()->values()->all())->toBe(['https://news.example.ca/two', 'https://www.cbc.ca/news/one']);
});

it('caps stories per run and spreads them across feeds', function () {
    fakeFeeds([
        'https://a.example.ca/rss' => ['body' => rssFeed([
            ['A1', 'https://a.example.ca/1', now()->subMinutes(1)],
            ['A2', 'https://a.example.ca/2', now()->subMinutes(2)],
            ['A3', 'https://a.example.ca/3', now()->subMinutes(3)],
        ])],
        'https://b.example.ca/rss' => ['body' => rssFeed([
            ['B1', 'https://b.example.ca/1', now()->subMinutes(10)],
            ['B2', 'https://b.example.ca/2', now()->subMinutes(11)],
        ])],
        'https://c.example.ca/rss' => ['body' => rssFeed([
            ['C1', 'https://c.example.ca/1', now()->subMinutes(20)],
        ])],
    ]);
    config(['news.max_stories_per_run' => 4, 'news.max_stories_per_feed' => 3]);

    $this->artisan('news:fetch')->assertSuccessful();

    expect(Story::pluck('title')->sort()->values()->all())->toBe(['A1', 'A2', 'B1', 'C1']);
});

it('caps stories per feed', function () {
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([
        ['Oldest', 'https://news.example.ca/1', now()->subHours(3)],
        ['Newest', 'https://news.example.ca/2', now()->subHour()],
        ['Middle', 'https://news.example.ca/3', now()->subHours(2)],
    ])]]);

    $this->artisan('news:fetch', ['--limit' => 10, '--per-feed' => 2])->assertSuccessful();

    expect(Story::pluck('title')->sort()->values()->all())->toBe(['Middle', 'Newest']);
});

it('uses the configured default caps', function () {
    fakeFeeds(collect(range(1, 4))->mapWithKeys(fn (int $feed) => ["https://f{$feed}.example.ca/rss" => ['body' => rssFeed(
        collect(range(1, 3))->map(fn (int $item) => ["F{$feed}-{$item}", "https://f{$feed}.example.ca/{$item}"])->all(),
    )]])->all());

    $this->artisan('news:fetch')->assertSuccessful();

    expect(Story::count())->toBe(5)
        ->and(Story::all()->countBy(fn (Story $story) => $story->domain())->max())->toBe(2);
});

it('skips old, undated, untitled, and unlinked items', function () {
    config(['news.max_age_hours' => 48]);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([
        ['Too old', 'https://news.example.ca/old', now()->subHours(49)],
        ['No date', 'https://news.example.ca/undated', null],
        ['   ', 'https://news.example.ca/untitled'],
        ['No link', ''],
        ['Not a web link', 'javascript:alert(1)'],
        ['Recent enough', 'https://news.example.ca/recent', now()->subHours(47)],
    ])]]);

    $this->artisan('news:fetch', ['--limit' => 10, '--per-feed' => 10])->assertSuccessful();

    expect(Story::sole()->title)->toBe('Recent enough');
});

it('shortens titles that are too long for a story', function () {
    $longTitle = str_repeat('Long headline words ', 10);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([[$longTitle, 'https://news.example.ca/long']])]]);

    $this->artisan('news:fetch')->assertSuccessful();

    $title = Story::sole()->title;
    expect(mb_strlen($title))->toBeLessThanOrEqual(80)
        ->and($title)->toEndWith('…')
        ->and($title)->toStartWith('Long headline words Long headline words');
});

it('reads atom feeds', function () {
    $atom = '<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom"><title>Atom</title>'
        .'<entry><title type="html">Ottawa &amp;lt;b&amp;gt;votes&amp;lt;/b&amp;gt;</title>'
        .'<link rel="self" href="https://news.example.ca/self"/><link rel="alternate" href="https://news.example.ca/atom-story"/>'
        .'<updated>'.now()->subHour()->toAtomString().'</updated></entry></feed>';
    fakeFeeds(['https://news.example.ca/atom' => ['politics' => true, 'body' => $atom]]);

    $this->artisan('news:fetch')->assertSuccessful();

    $story = Story::sole();
    expect($story->title)->toBe('Ottawa votes')
        ->and($story->url)->toBe('https://news.example.ca/atom-story')
        ->and($story->is_political)->toBeTrue();
});

it('keeps going when a feed errors or is not a feed', function () {
    Log::spy();
    fakeFeeds([
        'https://broken.example.ca/rss' => ['name' => 'Broken', 'status' => 500, 'body' => 'Server error'],
        'https://garbage.example.ca/rss' => ['name' => 'Garbage', 'body' => '<html><body>not <a feed'],
        'https://html.example.ca/rss' => ['name' => 'Html', 'body' => '<html><body>A web page</body></html>'],
        'https://good.example.ca/rss' => ['name' => 'Good', 'body' => rssFeed([['Still posted', 'https://good.example.ca/story']])],
    ]);

    $this->artisan('news:fetch')
        ->expectsOutputToContain('Skipped feed Broken')
        ->expectsOutputToContain('Skipped feed Garbage')
        ->expectsOutputToContain('Skipped feed Html')
        ->expectsOutputToContain('Queued 1 new story from 1 of 4 feeds.')
        ->assertSuccessful();

    expect(Story::sole()->title)->toBe('Still posted');
    Log::shouldHaveReceived('warning')->times(3);
});

it('keeps going when a feed times out', function () {
    fakeFeeds(['https://good.example.ca/rss' => ['body' => rssFeed([['Still posted', 'https://good.example.ca/story']])]]);
    config(['news.feeds' => [
        ['name' => 'Slow', 'url' => 'https://slow.example.ca/rss', 'province' => null, 'politics' => false],
        ...config('news.feeds'),
    ]]);
    Http::fake(['https://slow.example.ca/rss' => Http::failedConnection()]);

    $this->artisan('news:fetch')->expectsOutputToContain('Skipped feed Slow')->assertSuccessful();

    expect(Story::sole()->title)->toBe('Still posted');
});

it('fails when every feed fails', function () {
    fakeFeeds(['https://broken.example.ca/rss' => ['status' => 503]]);

    $this->artisan('news:fetch')->assertFailed();
});

it('runs hourly without overlapping', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command, 'news:fetch'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});

it('queues one delayed job per picked story instead of posting inline', function () {
    Queue::fake();
    $this->freezeTime();
    fakeFeeds(collect(range(1, 10))->mapWithKeys(fn (int $feed) => ["https://f{$feed}.example.ca/rss" => ['body' => rssFeed([
        ["Story {$feed}", "https://f{$feed}.example.ca/story?utm_source=rss"],
        ["Older {$feed}", "https://f{$feed}.example.ca/older", now()->subHours(2)],
    ])]])->all());

    $this->artisan('news:fetch', ['--limit' => 10, '--per-feed' => 1])
        ->expectsOutputToContain('Queued 10 new stories from 10 of 10 feeds.')
        ->assertSuccessful();

    expect(Story::count())->toBe(0);
    Queue::assertPushed(PostNewsStory::class, 10);
    Queue::assertPushed(PostNewsStory::class, fn (PostNewsStory $job) => $job->attributes === [
        'title' => 'Story 3',
        'url' => 'https://f3.example.ca/story',
        'province' => null,
        'is_political' => false,
    ]);

    $delays = queuedDelays();
    expect($delays)->each->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(59 * 60)
        ->and(array_unique($delays))->toHaveCount(10);
});

it('spreads the delays across the configured window', function () {
    Queue::fake();
    $this->freezeTime();
    config(['news.post_delay_max_minutes' => 5]);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed(
        collect(range(1, 8))->map(fn (int $item) => ["Story {$item}", "https://news.example.ca/{$item}"])->all(),
    )]]);

    $this->artisan('news:fetch', ['--limit' => 8, '--per-feed' => 8])->assertSuccessful();

    $delays = queuedDelays();
    expect($delays)->toHaveCount(8)
        ->each->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(5 * 60)
        ->and(array_unique($delays))->toHaveCount(8);
});

it('queues nothing when the poster account does not exist', function () {
    Queue::fake();
    config(['news.poster_username' => 'nobody_here']);
    fakeFeeds(['https://news.example.ca/rss' => ['body' => rssFeed([['A story', 'https://news.example.ca/a']])]]);

    $this->artisan('news:fetch')->assertFailed();

    Queue::assertNothingPushed();
});
