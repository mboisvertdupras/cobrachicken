<x-layout title="Login">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Login</h2>
    <form method="POST" action="{{ route('login') }}" class="mb-6 max-w-sm space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">username</span>
                <x-input name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="off" autofocus />
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">password</span>
                <x-input type="password" name="password" autocomplete="current-password" />
            </label>
        <x-button>login</x-button>
    </form>
    <a href="{{ route('password.request') }}" class="text-red-700 hover:underline">Forgot your password?</a>

    <div class="mt-8 border-t border-stone-200 pt-6">
        <h2 class="mb-3 text-base font-semibold text-stone-900">Create Account</h2>
        <form method="POST" action="{{ route('register') }}" class="mb-6 max-w-sm space-y-3">
            @csrf
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">username</span>
                    <x-input name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="off" />
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">email</span>
                    <x-input type="email" name="email" value="{{ old('email') }}" autocomplete="email" />
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">password</span>
                    <x-input type="password" name="password" autocomplete="new-password" />
                </label>
                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-stone-700">confirm</span>
                    <x-input type="password" name="password_confirmation" autocomplete="new-password" />
                </label>
            <x-button>create account</x-button>
        </form>
    </div>
</x-layout>
