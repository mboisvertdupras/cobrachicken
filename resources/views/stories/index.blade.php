<x-layout :title="$title ?? null">
    @isset($showPolitics)
        <div class="mb-4 text-xs">
            <label class="inline-flex cursor-pointer items-center gap-1.5"><input type="checkbox" id="show-politics" class="size-4 rounded border-stone-300 accent-red-600 focus:ring-2 focus:ring-red-500" @checked($showPolitics)> show politics</label>
        </div>
    @endisset

    <div class="space-y-3">
        @forelse ($stories as $story)
            @include('stories._row', ['story' => $story, 'rank' => $stories->firstItem() + $loop->index])
        @empty
            <p class="text-xs text-stone-500">No stories yet.</p>
        @endforelse
    </div>
    @if ($stories->hasMorePages())
        <p class="mt-5 pl-[3.25rem]"><a href="{{ $stories->nextPageUrl() }}" rel="next" class="font-medium text-red-700 hover:underline">More</a></p>
    @endif
</x-layout>
