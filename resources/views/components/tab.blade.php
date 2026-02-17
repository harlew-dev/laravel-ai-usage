@props([
    'active' => false,
    'type' => 'button',
])

@php
$baseClasses = 'px-3 py-1 text-xs font-medium rounded transition-colors duration-150';
$activeClasses = 'bg-blue-500/20 text-blue-400 border border-blue-500/30';
$inactiveClasses = 'text-zinc-400 border border-zinc-700/20 hover:text-white hover:bg-zinc-700/50';

$classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

@if($type === 'button')
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif
