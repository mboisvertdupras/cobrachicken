<x-layout :title="$title ?? null">
    @isset($showPolitics)
        <div class="toolbar">
            <label><input type="checkbox" id="show-politics" @checked($showPolitics)> show politics</label>
        </div>
    @endisset

    <table class="itemlist">
        @forelse ($stories as $story)
            @include('stories._row', ['story' => $story, 'rank' => $stories->firstItem() + $loop->index])
            <tr class="spacer"></tr>
        @empty
            <tr><td class="subtext">No stories yet.</td></tr>
        @endforelse
        @if ($stories->hasMorePages())
            <tr><td colspan="2"></td><td class="title"><a href="{{ $stories->nextPageUrl() }}" class="morelink" rel="next">More</a></td></tr>
        @endif
    </table>
</x-layout>
