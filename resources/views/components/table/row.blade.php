@props([
    'align' => 'left',
])

@php
$alignClass = match($align) {
    'center' => 'text-center',
    'right' => 'text-right',
    default => 'text-left',
};
@endphp

<tr {{ $attributes->merge(['class' => 'border-b border-border-secondary transition-colors duration-100 hover:bg-background-hover ' . $alignClass]) }}>
    {{ $slot }}
</tr>
