<x-layout title="Submit">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Submit a story</h2>
    <form method="POST" action="{{ route('stories.store') }}" class="max-w-xl space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">title</span>
                <x-input name="title" value="{{ old('title') }}" maxlength="80" autofocus />
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">url</span>
                <x-input type="url" name="url" value="{{ old('url') }}" />
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">text</span>
                <x-input as="textarea" name="text" rows="4">{{ old('text') }}</x-input>
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">province</span>
                <x-input as="select" name="province">
                    <option value="">National</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->value }}" @selected(old('province') === $province->value)>{{ $province->label() }}</option>
                    @endforeach
                </x-input>
            </label>
            <label class="flex cursor-pointer items-center gap-2"><x-checkbox name="is_political" value="1" :checked="old('is_political')" /> politics</label>
        <x-button>submit</x-button>
        <p class="text-xs text-stone-500">Leave url blank to submit a question for discussion. If there is no url, text will appear at the top of the thread. If there is a url, text is optional.</p>
    </form>
</x-layout>
