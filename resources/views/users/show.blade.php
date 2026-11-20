<x-layout :title="'Profile: '.$user->username">
    <table class="profile">
        <tr><td>user:</td><td>{{ $user->username }}</td></tr>
        <tr><td>created:</td><td>{{ $user->created_at->toFormattedDateString() }}</td></tr>
        <tr><td>karma:</td><td>{{ $user->karma }}</td></tr>
        <tr><td valign="top">about:</td><td>
            @if (auth()->user()?->is($user))
                <form method="POST" action="{{ route('users.update') }}">
                    @csrf
                    @method('PATCH')
                    <textarea name="about" rows="5" cols="60">{{ old('about', $user->about) }}</textarea>
                    <br><input type="submit" value="update">
                </form>
            @elseif ($user->about)
                <div class="comment">{{ App\Support\PlainText::format($user->about) }}</div>
            @endif
        </td></tr>
        <tr><td></td><td><a href="{{ route('users.submissions', $user) }}">submissions</a></td></tr>
        <tr><td></td><td><a href="{{ route('users.comments', $user) }}">comments</a></td></tr>
    </table>
</x-layout>
