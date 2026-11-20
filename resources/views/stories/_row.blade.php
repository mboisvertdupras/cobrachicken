<div class="flex items-start gap-1.5" id="story-{{ $story->id }}">
    @if ($rank)
        <div class="w-7 shrink-0 pt-px text-right text-stone-400">{{ $rank }}.</div>
    @endif
    <div class="votelinks shrink-0 pt-0.5">@include('partials.vote-arrow', ['item' => $story])</div>
    <div class="min-w-0">
        <div class="text-base leading-snug">
            <a href="{{ $story->link() }}" class="font-medium text-stone-900 hover:text-red-700">{{ $story->title }}</a>
            @if ($story->domain())
                <span class="text-xs text-stone-500">({{ $story->domain() }})</span>
            @endif
            @if ($story->province)
                <a href="{{ route('provinces.show', $story->province) }}" class="rounded bg-red-50 px-1 text-xs font-medium text-red-700 hover:bg-red-100" title="{{ $story->province->label() }}">{{ $story->province->value }}</a>
            @endif
            @if ($story->is_political)
                <span class="rounded bg-stone-100 px-1 text-xs font-medium text-stone-600">politics</span>
            @endif
        </div>
        <div class="text-xs text-stone-500">
            <span data-points="story-{{ $story->id }}">{{ $story->points }} {{ Str::plural('point', $story->points) }}</span>
            by <a href="{{ route('users.show', $story->user) }}" class="hover:text-red-700 hover:underline">{{ $story->user->username }}</a>
            <a href="{{ route('stories.show', $story) }}" title="{{ $story->created_at->toDayDateTimeString() }}" class="hover:underline">{{ $story->created_at->diffForHumans() }}</a>
            @include('partials.unvote', ['item' => $story])
            | <a href="{{ route('stories.show', $story) }}" class="hover:text-red-700 hover:underline">{{ $story->comments_count }} {{ Str::plural('comment', $story->comments_count) }}</a>
        </div>
    </div>
</div>
