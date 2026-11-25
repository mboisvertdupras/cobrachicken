<?php

namespace App\Console\Commands;

use App\Actions\SubmitStory;
use App\Enums\Province;
use App\Jobs\PostNewsStory;
use App\Models\Story;
use App\Models\User;
use App\Support\FeedParser;
use App\Support\StoryUrl;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

/**
 * @phpstan-type FeedConfig array{name: string, url: string, province?: Province|string|null, politics?: bool}
 * @phpstan-type Candidate array{feed: string, title: string, url: string, province: ?Province, is_political: bool, published_at: CarbonImmutable}
 */
#[Signature('news:fetch
    {--limit= : Most stories to post in this run (default: news.max_stories_per_run)}
    {--per-feed= : Most stories to post from one feed in this run (default: news.max_stories_per_feed)}')]
#[Description('Queue new stories from the Canadian news feeds in config/news.php, each posted after a random delay')]
class FetchNews extends Command
{
    private const TITLE_MAX_LENGTH = 80;

    public function handle(FeedParser $parser): int
    {
        $username = (string) config('news.poster_username');
        $poster = User::usernameIs($username)->first();

        if ($poster === null) {
            $this->error("The news poster account \"{$username}\" does not exist. Create it first, or set NEWS_POSTER_USERNAME to an existing username.");

            return self::FAILURE;
        }

        $limit = max(0, (int) ($this->option('limit') ?? config('news.max_stories_per_run')));
        $perFeed = max(0, (int) ($this->option('per-feed') ?? config('news.max_stories_per_feed')));
        $oldestAllowed = CarbonImmutable::now()->subHours((int) config('news.max_age_hours'));

        /** @var list<FeedConfig> $feeds */
        $feeds = config('news.feeds', []);
        $candidatesByUrl = [];
        $failedFeeds = 0;

        foreach ($feeds as $feed) {
            try {
                $province = $this->province($feed['province'] ?? null);
                $items = $parser->parse($this->download($feed['url']));
            } catch (Throwable $exception) {
                $failedFeeds++;
                $this->warn("Skipped feed {$feed['name']}: {$exception->getMessage()}");
                Log::warning('news:fetch could not read a feed.', ['feed' => $feed['name'], 'url' => $feed['url'], 'error' => $exception->getMessage()]);

                continue;
            }

            foreach ($items as $item) {
                $title = $this->cleanTitle($item['title']);
                $url = StoryUrl::normalize(html_entity_decode($item['url'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if ($title === '' || $url === '' || $item['published_at'] === null || $item['published_at']->lt($oldestAllowed)) {
                    continue;
                }

                if (isset($candidatesByUrl[$url])) {
                    if ($province !== null) {
                        $candidatesByUrl[$url]['provinces'][$province->value] = $province;
                    }

                    $candidatesByUrl[$url]['is_political'] = $candidatesByUrl[$url]['is_political'] || (bool) ($feed['politics'] ?? false);

                    continue;
                }

                $candidatesByUrl[$url] = [
                    'feed' => $feed['name'],
                    'title' => $title,
                    'url' => $url,
                    'provinces' => $province ? [$province->value => $province] : [],
                    'is_political' => (bool) ($feed['politics'] ?? false),
                    'published_at' => $item['published_at'],
                ];
            }
        }

        $candidatesByUrl = array_map(fn (array $candidate): array => [
            ...Arr::except($candidate, 'provinces'),
            'province' => count($candidate['provinces']) === 1 ? reset($candidate['provinces']) : null,
        ], $candidatesByUrl);

        $picked = array_slice($this->spreadAcrossFeeds($candidatesByUrl, $perFeed), 0, $limit);
        $delays = $this->postingDelays(count($picked), (int) config('news.post_delay_max_minutes') * 60);

        foreach ($picked as $index => $candidate) {
            $postAt = CarbonImmutable::now()->addSeconds($delays[$index]);
            PostNewsStory::dispatch($this->storyAttributes($candidate))->delay($postAt);

            $tags = array_filter([$candidate['province']?->value, $candidate['is_political'] ? 'politics' : null]);
            $this->line("Queued [{$candidate['feed']}]".($tags ? ' ['.implode(', ', $tags).']' : '')." {$candidate['title']} (in ".intdiv($delays[$index], 60).'m '.($delays[$index] % 60)."s, at {$postAt->format('H:i:s')})");
        }

        $queued = count($picked);
        $this->info("Queued {$queued} new ".Str::plural('story', $queued).' from '.(count($feeds) - $failedFeeds).' of '.count($feeds).' feeds.');

        return $feeds !== [] && $failedFeeds === count($feeds) ? self::FAILURE : self::SUCCESS;
    }

    private function download(string $url): string
    {
        return Http::timeout((int) config('news.timeout_seconds'))
            ->connectTimeout((int) config('news.timeout_seconds'))
            ->withUserAgent((string) config('news.user_agent'))
            ->accept('application/rss+xml, application/atom+xml, application/xml;q=0.9, text/xml;q=0.8, */*;q=0.5')
            ->get($url)
            ->throw()
            ->body();
    }

    private function province(Province|string|null $province): ?Province
    {
        return $province === null || $province instanceof Province ? $province : Province::from($province);
    }

    /**
     * Decode HTML entities (feeds sometimes encode them twice), drop tags, collapse
     * whitespace, and shorten the title to fit the story title column.
     */
    private function cleanTitle(string $title): string
    {
        for ($pass = 0; $pass < 2; $pass++) {
            $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $title = Str::squish(strip_tags($title));

        if (mb_strlen($title) > self::TITLE_MAX_LENGTH) {
            $title = rtrim(mb_substr($title, 0, self::TITLE_MAX_LENGTH - 1)).'…';
        }

        return $title;
    }

    /**
     * Pick up to $perFeed postable stories from each feed, newest first, then order them
     * round by round (every feed's newest pick, then every feed's second pick) so that a
     * per-run limit takes stories from many feeds instead of exhausting the first one.
     *
     * @param  array<string, Candidate>  $candidatesByUrl
     * @return list<Candidate>
     */
    private function spreadAcrossFeeds(array $candidatesByUrl, int $perFeed): array
    {
        $rounds = [];

        foreach (collect($candidatesByUrl)->groupBy('feed') as $candidates) {
            $round = 0;

            foreach ($candidates->sortByDesc('published_at') as $candidate) {
                if ($round >= $perFeed) {
                    break;
                }

                if (Story::isUrlPosted($candidate['url']) || Validator::make($this->storyAttributes($candidate), SubmitStory::rules())->fails()) {
                    continue;
                }

                $rounds[$round++][] = $candidate;
            }
        }

        return collect($rounds)
            ->flatMap(fn (array $round) => collect($round)->sortByDesc('published_at')->values())
            ->values()
            ->all();
    }

    /**
     * Distinct random delays in seconds, from 0 to $maxSeconds, in ascending order so the
     * first pick posts first. The default window ends before the next hourly run starts.
     * Delays repeat only when there are more stories than seconds in the window.
     *
     * @return list<int>
     */
    private function postingDelays(int $count, int $maxSeconds): array
    {
        $maxSeconds = max(0, $maxSeconds);
        $delays = [];

        while (count($delays) < $count) {
            $delay = random_int(0, $maxSeconds);

            if (! in_array($delay, $delays, true) || count($delays) > $maxSeconds) {
                $delays[] = $delay;
            }
        }

        sort($delays);

        return $delays;
    }

    /**
     * @param  Candidate  $candidate
     * @return array{title: string, url: string, province: ?string, is_political: bool}
     */
    private function storyAttributes(array $candidate): array
    {
        return [
            'title' => $candidate['title'],
            'url' => $candidate['url'],
            'province' => $candidate['province']?->value,
            'is_political' => $candidate['is_political'],
        ];
    }
}
