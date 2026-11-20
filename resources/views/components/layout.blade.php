@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="origin">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | ' : '' }}{{ config('app.name') }}</title>
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }}" href="{{ route('rss') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 font-sans text-sm text-stone-600 antialiased">
<div class="mx-auto max-w-4xl bg-white sm:my-4 sm:overflow-hidden sm:shadow-sm sm:ring-1 sm:ring-stone-200">
    <header class="flex flex-wrap items-center gap-x-3 gap-y-1 bg-red-600 px-3 py-2 text-white">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-white"><span class="flex size-6 items-center justify-center border border-white text-sm">C</span><span class="hover:underline">{{ config('app.name') }}</span></a>
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <a href="{{ route('newest') }}" class="hover:underline">new</a>
            <span class="text-red-300">|</span>
            <a href="{{ route('comments.index') }}" class="hover:underline">comments</a>
            <span class="text-red-300">|</span>
            <a href="{{ route('ask') }}" class="hover:underline">ask</a>
            <span class="text-red-300">|</span>
            <a href="{{ route('provinces.index') }}" class="hover:underline">provinces</a>
            <span class="text-red-300">|</span>
            <a href="{{ route('stories.create') }}" class="hover:underline">submit</a>
        </nav>
        <div class="ml-auto flex items-center gap-2">
            @auth
                <a href="{{ route('users.show', auth()->user()) }}" class="hover:underline">{{ auth()->user()->username }}</a>
                <span class="text-red-200">({{ auth()->user()->karma }})</span>
                <span class="text-red-300">|</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="cursor-pointer hover:underline">logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:underline">login</a>
            @endauth
        </div>
    </header>

    <main class="px-3 py-4 sm:px-4">
        {{ $slot }}
    </main>

    <footer class="border-t border-stone-200 px-4 py-4 text-center text-xs text-stone-500">
        <a href="{{ route('faq') }}" class="hover:text-red-700 hover:underline">FAQ</a> |
        <a href="{{ route('about') }}" class="hover:text-red-700 hover:underline">About</a> |
        <a href="{{ route('rss') }}" class="hover:text-red-700 hover:underline">RSS</a> |
        <a href="{{ route('stories.create') }}" class="hover:text-red-700 hover:underline">Submit</a>
    </footer>
</div>
</body>
</html>
