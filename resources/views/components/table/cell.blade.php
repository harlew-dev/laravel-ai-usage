@props([
    'align' => 'left',
    'variant' => 'text',
])

@php
$alignClass = match($align) {
    'center' => 'text-center',
    'right' => 'text-right',
    default => 'text-left',
};

$variantClass = match($variant) {
    'text' => 'text-white',
    'title' => 'text-white font-mono font-medium',
    'code' => 'text-zinc-400 font-mono',
};
@endphp

<td {{ $attributes->merge(['class' => 'px-4 py-3 text-sm whitespace-nowrap ' . $variantClass . ' ' . $alignClass]) }}>
    {{ $slot }}
</td>
