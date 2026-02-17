@props([
    'content',
    'position' => 'top',
])

@php
$positionClasses = match($position) {
    'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
    'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
    'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    default => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
};
@endphp

<span class="group relative inline-flex" {{ $attributes }}>
    {{ $slot }}
    <span class="absolute {{ $positionClasses }} px-2 py-1 bg-background-tertiary border border-border rounded text-xs text-white whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[100] pointer-events-none">
        {{ $content }}
    </span>
</span>
