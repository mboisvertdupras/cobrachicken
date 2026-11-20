<tr class="athing" id="story-{{ $story->id }}">
    <td class="rank">{{ $rank ? $rank.'.' : '' }}</td>
    <td class="votelinks">@include('partials.vote-arrow', ['item' => $story])</td>
    <td class="title">
        <a href="{{ $story->link() }}" class="titlelink">{{ $story->title }}</a>
        @if ($story->domain())
            <span class="sitestr">({{ $story->domain() }})</span>
        @endif
        @if ($story->province)
            <a href="{{ route('provinces.show', $story->province) }}" class="tag" title="{{ $story->province->label() }}">[{{ $story->province->value }}]</a>
        @endif
        @if ($story->is_political)
            <span class="tag">politics</span>
        @endif
    </td>
</tr>
<tr>
    <td colspan="2"></td>
    <td class="subtext">
        <span data-points="story-{{ $story->id }}">{{ $story->points }} {{ Str::plural('point', $story->points) }}</span>
        by <a href="{{ route('users.show', $story->user) }}">{{ $story->user->username }}</a>
        <a href="{{ route('stories.show', $story) }}" title="{{ $story->created_at->toDayDateTimeString() }}">{{ $story->created_at->diffForHumans() }}</a>
        @include('partials.unvote', ['item' => $story])
        | <a href="{{ route('stories.show', $story) }}">{{ $story->comments_count }} {{ Str::plural('comment', $story->comments_count) }}</a>
    </td>
</tr>
