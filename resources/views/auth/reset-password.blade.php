<x-layout title="Reset password">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Choose a new password</h2>
    <form method="POST" action="{{ route('password.update') }}" class="mb-6 max-w-sm space-y-3">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">email</span>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" class="block w-full border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="email">
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">password</span>
                <x-input type="password" name="password" autocomplete="new-password" autofocus />
            </label>
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">confirm</span>
                <x-input type="password" name="password_confirmation" autocomplete="new-password" />
            </label>
        <x-button>change password</x-button>
    </form>
</x-layout>
