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
                    <x-input as="textarea" name="about" rows="5">{{ old('about', $user->about) }}</x-input>
                    <x-button>update</x-button>
                </form>
            @elseif ($user->about)
                <div class="comment space-y-2 text-stone-900 [&_a]:underline">{{ App\Support\PlainText::format($user->about) }}</div>
            @endif
        </dd>
        <dt></dt><dd><a href="{{ route('users.submissions', $user) }}" class="text-red-700 hover:underline">submissions</a></dd>
        <dt></dt><dd><a href="{{ route('users.comments', $user) }}" class="text-red-700 hover:underline">comments</a></dd>
    </dl>
</x-layout>
