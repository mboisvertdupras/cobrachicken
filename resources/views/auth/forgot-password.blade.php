<x-layout title="Reset password">
    <x-errors />

    <h2 class="mb-3 text-base font-semibold text-stone-900">Reset your password</h2>
    <form method="POST" action="{{ route('password.email') }}" class="mb-6 max-w-sm space-y-3">
        @csrf
            <label class="block">
                <span class="mb-1 block text-xs font-medium text-stone-700">email</span>
                <x-input type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus />
            </label>
        <x-button>send reset email</x-button>
    </form>
</x-layout>
