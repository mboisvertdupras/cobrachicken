@auth
    @if ($item->voteControlFor(auth()->user()) !== null)
        <form method="POST" action="{{ route('votes.destroy') }}" class="js-vote inline" data-how="un" data-votable="{{ $item->getMorphClass() }}-{{ $item->id }}" @if (! $item->voted) hidden @endif>
            @csrf
            @method('DELETE')
            <input type="hidden" name="type" value="{{ $item->getMorphClass() }}">
            <input type="hidden" name="id" value="{{ $item->id }}">
            | <button type="submit" class="linkbutton">unvote</button>
        </form>
    @endif
@endauth
