<x-layout :title="$story->title">
    @include('stories._row', ['story' => $story, 'rank' => null])
    @if ($story->text)
        <div class="mt-3 space-y-2 text-sm text-stone-900 sm:pl-9 [&_a]:underline">{{ App\Support\PlainText::format($story->text) }}</div>
    @endif
    <div class="mt-4 sm:pl-9">
        @auth
            <form method="POST" action="{{ route('comments.store', $story) }}" class="js-comment" data-target="#comment-tree">
                @csrf
                <textarea name="body" rows="5" class="block w-full max-w-xl rounded-md border border-stone-300 px-3 py-2 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40"></textarea>
                <div class="formerrors text-red-700"></div>
                <input type="submit" value="add comment" class="mt-2 cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
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
