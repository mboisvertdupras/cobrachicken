<x-layout title="Reset password">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Reset your password</h2>
    <form method="POST" action="{{ route('password.email') }}" class="mb-6 max-w-sm space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">email</span>
                <input type="email" name="email" value="{{ old('email') }}" class="block w-full rounded-md border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40" autocomplete="email" autofocus>
            </label>
        <input type="submit" value="send reset email" class="cursor-pointer rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
    </form>
</x-layout>
