<x-layout title="Provinces and territories">
    <p class="mb-3">Stories tagged with a province or territory:</p>
    <ul class="grid grid-cols-1 gap-x-6 gap-y-1 sm:grid-cols-2">
        @foreach ($provinces as $province)
            <li><a href="{{ route('provinces.show', $province) }}" class="font-medium text-stone-900 hover:text-red-700 hover:underline">{{ $province->label() }}</a> <span class="text-xs text-stone-500">({{ $province->value }})</span></li>
        @endforeach
    </ul>
</x-layout>
