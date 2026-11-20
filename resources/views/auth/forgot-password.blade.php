<x-layout title="Reset password">
    <x-errors />

    <b>Reset your password</b>
    <form method="POST" action="{{ route('password.email') }}" class="authform">
        @csrf
        <table>
            <tr><td>email:</td><td><input type="email" name="email" value="{{ old('email') }}" size="20" autocomplete="email" autofocus></td></tr>
        </table>
        <input type="submit" value="send reset email">
    </form>
</x-layout>
