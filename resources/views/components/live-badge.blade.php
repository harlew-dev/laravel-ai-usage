@props([
    'tooltip' => 'This data updates every 15 seconds',
])

<x-ai-usage::tooltip :content="$tooltip" position="bottom">
    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-xs font-medium text-emerald-400">
        <span class="relative flex h-1.5 w-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
        </span>
        LIVE
    </span>
</x-ai-usage::tooltip>
