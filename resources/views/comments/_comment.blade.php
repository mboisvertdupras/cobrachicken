<div class="comtr space-y-4" id="comment-{{ $comment->id }}">
    <div class="row flex items-start gap-1.5">
        <div class="votelinks shrink-0">@include('partials.vote-arrow', ['item' => $comment])</div>
        <div class="min-w-0 flex-1">
            <div class="text-xs text-stone-500">
                <a href="{{ route('users.show', $comment->user) }}" class="font-medium text-stone-700 hover:text-red-700">{{ $comment->user->username }}</a>
                <a href="#comment-{{ $comment->id }}" title="{{ $comment->created_at->toDayDateTimeString() }}" class="hover:underline">{{ $comment->created_at->diffForHumans() }}</a>
                @include('partials.unvote', ['item' => $comment])
                <a href="#comment-{{ $comment->id }}" class="togg ml-1 hover:text-red-700">[-]</a>
            </div>
            <div class="commtext">
                <div class="comment mt-1 space-y-2 text-sm text-stone-900 [&_a]:underline">{{ App\Support\PlainText::format($comment->body) }}</div>
                <div class="reply mt-1 text-xs">
                    @auth
                        <a href="#reply-{{ $comment->id }}" class="js-reply text-stone-500 hover:text-red-700 hover:underline">reply</a>
                        <form method="POST" action="{{ route('comments.store', $comment->story_id) }}" id="reply-{{ $comment->id }}" class="js-comment replyform mt-2" data-target="#children-{{ $comment->id }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <x-input as="textarea" name="body" rows="4"></x-input>
                            <div class="formerrors text-red-700"></div>
                            <x-button class="mt-2">reply</x-button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-stone-500 hover:text-red-700 hover:underline">reply</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    <div class="children ml-3 space-y-4 border-l border-stone-200 pl-3 sm:ml-5 sm:pl-4" id="children-{{ $comment->id }}">
        @foreach ($commentsByParent->get($comment->id, []) as $child)
            @include('comments._comment', ['comment' => $child])
        @endforeach
    </div>
</div>
