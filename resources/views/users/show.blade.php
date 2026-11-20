<x-layout :title="'Profile: '.$user->username">
    <dl class="grid max-w-xl grid-cols-[6rem_1fr] gap-y-2">
        <dt class="text-stone-500">user</dt><dd class="font-medium text-stone-900">{{ $user->username }}</dd>
        <dt class="text-stone-500">created</dt><dd>{{ $user->created_at->toFormattedDateString() }}</dd>
        <dt class="text-stone-500">karma</dt><dd>{{ $user->karma }}</dd>
        <dt class="text-stone-500">about</dt>
        <dd>
            @if (auth()->user()?->is($user))
                <form method="POST" action="{{ route('users.update') }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <textarea name="about" rows="5" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40">{{ old('about', $user->about) }}</textarea>
                    <input type="submit" value="update" class="cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                </form>
            @elseif ($user->about)
                <div class="comment space-y-2 text-stone-900 [&_a]:underline">{{ App\Support\PlainText::format($user->about) }}</div>
            @endif
        </dd>
        <dt></dt><dd><a href="{{ route('users.submissions', $user) }}" class="text-red-700 hover:underline">submissions</a></dd>
        <dt></dt><dd><a href="{{ route('users.comments', $user) }}" class="text-red-700 hover:underline">comments</a></dd>
    </dl>
</x-layout>
