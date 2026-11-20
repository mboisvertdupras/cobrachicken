@props(['as' => 'input'])
@php
    $classes = 'block w-full border border-stone-300 px-3 py-1.5 text-stone-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40';
@endphp
@if ($as === 'input')
    <input {{ $attributes->merge(['type' => 'text', 'class' => $classes]) }}>
@else
    <{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</{{ $as }}>
@endif
