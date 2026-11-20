@php($control = $item->voteControlFor(auth()->user()))
@if ($control === null)
    <span class="nosee block size-5"></span>
@elseguest
    <a href="{{ route('login') }}" title="upvote" class="block size-5 bg-stone-400 [clip-path:polygon(50%_20%,0_80%,100%_80%)] hover:bg-red-600"></a>
@else
    <form method="POST" action="{{ route('votes.store') }}" class="js-vote {{ $control === 'un' ? 'nosee' : '' }}" data-how="up" data-votable="{{ $item->getMorphClass() }}-{{ $item->id }}">
        @csrf
        <input type="hidden" name="type" value="{{ $item->getMorphClass() }}">
        <input type="hidden" name="id" value="{{ $item->id }}">
        <button type="submit" title="upvote" class="block size-5 cursor-pointer bg-stone-400 [clip-path:polygon(50%_20%,0_80%,100%_80%)] hover:bg-red-600 focus:outline-none focus-visible:bg-red-600"></button>
    </form>
@endif
