<div class="comtr" id="comment-{{ $comment->id }}">
    <table>
        <tr>
            <td class="votelinks">@include('partials.vote-arrow', ['item' => $comment])</td>
            <td>
                <div class="comhead">
                    <a href="{{ route('users.show', $comment->user) }}" class="hnuser">{{ $comment->user->username }}</a>
                    <a href="#comment-{{ $comment->id }}" title="{{ $comment->created_at->toDayDateTimeString() }}">{{ $comment->created_at->diffForHumans() }}</a>
                    @include('partials.unvote', ['item' => $comment])
                    <a href="#comment-{{ $comment->id }}" class="togg">[-]</a>
                </div>
                <div class="commtext">
                    <div class="comment">{{ App\Support\PlainText::format($comment->body) }}</div>
                    <div class="reply">
                        @auth
                            <a href="#reply-{{ $comment->id }}" class="js-reply">reply</a>
                            <form method="POST" action="{{ route('comments.store', $comment->story_id) }}" id="reply-{{ $comment->id }}" class="js-comment replyform" data-target="#children-{{ $comment->id }}">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <textarea name="body" rows="8" cols="80"></textarea>
                                <div class="formerrors"></div>
                                <input type="submit" value="reply">
                            </form>
                        @else
                            <a href="{{ route('login') }}">reply</a>
                        @endauth
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="children" id="children-{{ $comment->id }}">
        @foreach ($commentsByParent->get($comment->id, []) as $child)
            @include('comments._comment', ['comment' => $child])
        @endforeach
    </div>
</div>
