<x-layout :title="$title">
    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div class="comtr">
                <div class="row flex items-start gap-1.5">
                    <div class="votelinks shrink-0">@include('partials.vote-arrow', ['item' => $comment])</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs text-stone-500">
                            <a href="{{ route('users.show', $comment->user) }}" class="font-medium text-stone-700 hover:text-red-700">{{ $comment->user->username }}</a>
                            <a href="{{ route('stories.show', $comment->story) }}#comment-{{ $comment->id }}" class="hover:underline">{{ $comment->created_at->diffForHumans() }}</a>
                            @include('partials.unvote', ['item' => $comment])
                            | on: <a href="{{ route('stories.show', $comment->story) }}" class="hover:text-red-700 hover:underline">{{ $comment->story->title }}</a>
                        </div>
                        <div class="comment mt-1 space-y-2 text-sm text-stone-900 [&_a]:underline">{{ App\Support\PlainText::format($comment->body) }}</div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-xs text-stone-500">No comments yet.</p>
        @endforelse
    </div>

    @if ($comments->hasMorePages())
        <p class="mt-5"><a href="{{ $comments->nextPageUrl() }}" rel="next" class="font-medium text-red-700 hover:underline">More</a></p>
    @endif
</x-layout>
