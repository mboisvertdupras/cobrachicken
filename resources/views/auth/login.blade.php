<x-layout title="Login">
    <x-errors />

    <b>Login</b>
    <form method="POST" action="{{ route('login') }}" class="authform">
        @csrf
        <table>
            <tr><td>username:</td><td><input type="text" name="username" value="{{ old('username') }}" size="20" autocomplete="username" autocapitalize="off" autofocus></td></tr>
            <tr><td>password:</td><td><input type="password" name="password" size="20" autocomplete="current-password"></td></tr>
        </table>
        <input type="submit" value="login">
    </form>
    <a href="{{ route('password.request') }}">Forgot your password?</a>

    <br><br>
    <b>Create Account</b>
    <form method="POST" action="{{ route('register') }}" class="authform">
        @csrf
        <table>
            <tr><td>username:</td><td><input type="text" name="username" value="{{ old('username') }}" size="20" autocomplete="username" autocapitalize="off"></td></tr>
            <tr><td>email:</td><td><input type="email" name="email" value="{{ old('email') }}" size="20" autocomplete="email"></td></tr>
            <tr><td>password:</td><td><input type="password" name="password" size="20" autocomplete="new-password"></td></tr>
            <tr><td>confirm:</td><td><input type="password" name="password_confirmation" size="20" autocomplete="new-password"></td></tr>
        </table>
        <input type="submit" value="create account">
    </form>
</x-layout>
