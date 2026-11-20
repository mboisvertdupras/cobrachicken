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
<body>
<div id="main">
    <header class="topbar">
        <a href="{{ route('home') }}" class="logo"><span class="logo-box">C</span></a>
        <span class="pagetop">
            <b class="sitename"><a href="{{ route('home') }}">{{ config('app.name') }}</a></b>
            <a href="{{ route('newest') }}">new</a> |
            <a href="{{ route('comments.index') }}">comments</a> |
            <a href="{{ route('ask') }}">ask</a> |
            <a href="{{ route('provinces.index') }}">provinces</a> |
            <a href="{{ route('stories.create') }}">submit</a>
        </span>
        <span class="pagetop account">
            @auth
                <a href="{{ route('users.show', auth()->user()) }}">{{ auth()->user()->username }}</a> ({{ auth()->user()->karma }}) |
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="linkbutton">logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">login</a>
            @endauth
        </span>
    </header>

    <main class="content">
        {{ $slot }}
    </main>

    <footer class="footer">
        <a href="{{ route('faq') }}">FAQ</a> |
        <a href="{{ route('about') }}">About</a> |
        <a href="{{ route('rss') }}">RSS</a> |
        <a href="{{ route('stories.create') }}">Submit</a>
    </footer>
</div>
</body>
</html>
