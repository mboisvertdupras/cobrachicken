<x-layout :title="$title">
    @forelse ($comments as $comment)
        <div class="comtr">
            <table>
                <tr>
                    <td class="votelinks">@include('partials.vote-arrow', ['item' => $comment])</td>
                    <td>
                        <div class="comhead">
                            <a href="{{ route('users.show', $comment->user) }}" class="hnuser">{{ $comment->user->username }}</a>
                            <a href="{{ route('stories.show', $comment->story) }}#comment-{{ $comment->id }}">{{ $comment->created_at->diffForHumans() }}</a>
                            @include('partials.unvote', ['item' => $comment])
                            | on: <a href="{{ route('stories.show', $comment->story) }}">{{ $comment->story->title }}</a>
                        </div>
                        <div class="comment">{{ App\Support\PlainText::format($comment->body) }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @empty
        <p class="subtext">No comments yet.</p>
    @endforelse

    @if ($comments->hasMorePages())
        <p><a href="{{ $comments->nextPageUrl() }}" class="morelink" rel="next">More</a></p>
    @endif
</x-layout>
