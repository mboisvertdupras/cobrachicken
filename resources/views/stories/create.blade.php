<x-layout title="Submit">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Submit a story</h2>
    <form method="POST" action="{{ route('stories.store') }}" class="max-w-xl space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">title</span>
                <input type="text" name="title" value="{{ old('title') }}" maxlength="80" autofocus class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40">
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">url</span>
                <input type="url" name="url" value="{{ old('url') }}" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40">
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">text</span>
                <textarea name="text" rows="4" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40">{{ old('text') }}</textarea>
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">province</span>
                <select name="province" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                    <option value="">National</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->value }}" @selected(old('province') === $province->value)>{{ $province->label() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" name="is_political" value="1" @checked(old('is_political')) class="size-4 rounded border-stone-300 accent-red-600"> politics</label>
        <input type="submit" value="submit" class="cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
        <p class="text-xs text-stone-500">Leave url blank to submit a question for discussion. If there is no url, text will appear at the top of the thread. If there is a url, text is optional.</p>
    </form>
</x-layout>
