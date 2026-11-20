<x-layout :title="$story->title">
    <table class="itemlist">
        @include('stories._row', ['story' => $story, 'rank' => null])
        @if ($story->text)
            <tr><td colspan="2"></td><td class="toptext">{{ App\Support\PlainText::format($story->text) }}</td></tr>
        @endif
        <tr><td colspan="2"></td><td>
            @auth
                <form method="POST" action="{{ route('comments.store', $story) }}" class="js-comment" data-target="#comment-tree">
                    @csrf
                    <textarea name="body" rows="8" cols="80"></textarea>
                    <div class="formerrors"></div>
                    <input type="submit" value="add comment">
                </form>
            @else
                <p class="subtext"><a href="{{ route('login') }}">Log in</a> to comment.</p>
            @endauth
        </td></tr>
    </table>

    <div id="comment-tree" class="comment-tree">
        @foreach ($commentsByParent->get(0, []) as $comment)
            @include('comments._comment', ['comment' => $comment])
        @endforeach
    </div>
</x-layout>
