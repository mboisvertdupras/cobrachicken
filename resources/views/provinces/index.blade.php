<x-layout title="Provinces and territories">
    <p>Stories tagged with a province or territory:</p>
    <ul class="provincelist">
        @foreach ($provinces as $province)
            <li><a href="{{ route('provinces.show', $province) }}">{{ $province->label() }}</a> <span class="sitestr">({{ $province->value }})</span></li>
        @endforeach
    </ul>
</x-layout>
