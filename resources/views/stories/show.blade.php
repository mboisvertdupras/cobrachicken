<x-layout :title="$story->title">
    @include('stories._row', ['story' => $story, 'rank' => null])
    @if ($story->text)
        <div class="mt-3 space-y-2 text-sm text-stone-900 sm:pl-9 [&_a]:underline">{{ App\Support\PlainText::format($story->text) }}</div>
    @endif
    <div class="mt-4 sm:pl-9">
        @auth
            <form method="POST" action="{{ route('comments.store', $story) }}" class="js-comment" data-target="#comment-tree">
                @csrf
                <x-input as="textarea" name="body" rows="5" class="max-w-xl"></x-input>
                <div class="formerrors text-red-700"></div>
                <x-button class="mt-2">add comment</x-button>
            </form>
        @else
            <p class="text-xs text-stone-500"><a href="{{ route('login') }}" class="text-red-700 hover:underline">Log in</a> to comment.</p>
        @endauth
    </div>

    <div id="comment-tree" class="mt-6 space-y-4">
        @foreach ($commentsByParent->get(0, []) as $comment)
            @include('comments._comment', ['comment' => $comment])
        @endforeach
    </div>
</x-layout>
