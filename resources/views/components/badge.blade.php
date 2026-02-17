@props([
    'type',
])

@php
$styles = [
    'text' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
    'image' => 'bg-green-500/10 text-green-400 border-green-500/20 uppercase',
    'embedding' => 'bg-purple-500/10 text-purple-400 border-purple-500/20 uppercase',
    'audio' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20 uppercase',
    'default' => 'bg-zinc-700/10 text-zinc-400 border-zinc-700/20',
];

$style = $styles[strtolower($type)] ?? $styles['default'];
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $style }}">
    {{ $type }}
</span>
