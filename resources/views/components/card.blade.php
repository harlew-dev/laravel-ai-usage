@props([
    'label',
    'value',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'bg-background-tertiary border border-border rounded-lg p-4']) }}>
    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-1">{{ $label }}</p>
    <p class="text-xl font-semibold text-white mb-0.5">{{ $value }}</p>
    @if($description)
        <p class="text-xs text-zinc-600">{{ $description }}</p>
    @endif
</div>
