@php($control = $item->voteControlFor(auth()->user()))
@if ($control === null)
    <span class="votearrow nosee"></span>
@elseguest
    <a href="{{ route('login') }}" class="votearrow" title="upvote"></a>
@else
    <form method="POST" action="{{ route('votes.store') }}" class="js-vote inline {{ $control === 'un' ? 'nosee' : '' }}" data-how="up" data-votable="{{ $item->getMorphClass() }}-{{ $item->id }}">
        @csrf
        <input type="hidden" name="type" value="{{ $item->getMorphClass() }}">
        <input type="hidden" name="id" value="{{ $item->id }}">
        <button type="submit" class="votearrow" title="upvote"></button>
    </form>
@endif
