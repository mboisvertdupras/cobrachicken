<x-layout title="Reset password">
    <x-errors />

    <b>Choose a new password</b>
    <form method="POST" action="{{ route('password.update') }}" class="authform">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <table>
            <tr><td>email:</td><td><input type="email" name="email" value="{{ old('email', $request->email) }}" size="20" autocomplete="email"></td></tr>
            <tr><td>password:</td><td><input type="password" name="password" size="20" autocomplete="new-password" autofocus></td></tr>
            <tr><td>confirm:</td><td><input type="password" name="password_confirmation" size="20" autocomplete="new-password"></td></tr>
        </table>
        <input type="submit" value="change password">
    </form>
</x-layout>
