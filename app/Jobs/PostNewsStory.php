<?php

namespace App\Jobs;

use App\Actions\SubmitStory;
use App\Models\Story;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Post one story picked by news:fetch. The command dispatches these with random delays
 * so that a run's stories reach the site at different times instead of all at once.
 */
class PostNewsStory implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{title: string, url: string, province: ?string, is_political: bool}  $attributes  the url already normalized by StoryUrl::normalize()
     */
    public function __construct(public array $attributes) {}

    /**
     * Submit the story as the news poster, unless the poster account is gone or the
     * link was posted while this job waited in the queue.
     */
    public function handle(SubmitStory $submitStory): void
    {
        $username = (string) config('news.poster_username');
        $poster = User::usernameIs($username)->first();

        if ($poster === null) {
            Log::warning('PostNewsStory skipped a story because the news poster account does not exist.', ['username' => $username, 'url' => $this->attributes['url']]);

            return;
        }

        if (Story::isUrlPosted($this->attributes['url'])) {
            Log::info('PostNewsStory skipped a story that was posted while it waited in the queue.', ['url' => $this->attributes['url']]);

            return;
        }

        $submitStory->handle($poster, $this->attributes);
    }
}
