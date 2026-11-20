<x-layout title="Login">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Login</h2>
    <form method="POST" action="{{ route('login') }}" class="mb-6 max-w-sm space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">username</span>
                <input type="text" name="username" value="{{ old('username') }}" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="username" autocapitalize="off" autofocus>
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">password</span>
                <input type="password" name="password" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="current-password">
            </label>
        <input type="submit" value="login" class="cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
    </form>
    <a href="{{ route('password.request') }}" class="text-red-700 hover:underline">Forgot your password?</a>

    <div class="mt-8 border-t border-stone-200 pt-6">
        <h2 class="mb-3 text-base font-semibold text-stone-900">Create account</h2>
        <form method="POST" action="{{ route('register') }}" class="mb-6 max-w-sm space-y-3">
            @csrf
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">username</span>
                    <input type="text" name="username" value="{{ old('username') }}" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="username" autocapitalize="off">
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">email</span>
                    <input type="email" name="email" value="{{ old('email') }}" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="email">
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">password</span>
                    <input type="password" name="password" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="new-password">
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">confirm</span>
                    <input type="password" name="password_confirmation" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="new-password">
                </label>
            <input type="submit" value="create account" class="cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
        </form>
    </div>
</x-layout>
