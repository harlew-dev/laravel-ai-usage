@props([
    'types',
    'providers',
])

@php
$typeIcons = [
    'audio' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M2 10v3"/><path d="M6 6v11"/><path d="M10 3v18"/><path d="M14 8v7"/><path d="M18 5v13"/><path d="M22 10v4"/></svg>',
    'embedding' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>',
    'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>',
];

$providerCodes = [
    '302.AI' => '3A',
    'Anthropic' => 'AN',
    'Cohere' => 'CO',
    'Fireworks AI' => 'FW',
    'Meta' => 'ME',
    'Mistral' => 'MI',
    'OpenAI' => 'OP',
    'Together AI' => 'TA',
];
@endphp

<aside x-show="collapsed"
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="hidden lg:flex w-[55px] fixed left-0 top-0 bottom-0 bg-background-secondary border-r border-border z-50 lg:z-30 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden h-full flex-col">

    <div class="p-2 border-b border-border flex justify-center flex-shrink-0">
        <button @click="collapsed = false" class="group relative p-2 rounded-md bg-background-tertiary border border-border text-zinc-400 hover:text-white hover:bg-background-hover transition-all duration-150" aria-label="Expand sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 rotate-180">
                <path d="M3 19V5"></path>
                <path d="m13 6-6 6 6 6"></path>
                <path d="M7 12h14"></path>
            </svg>
        </button>
    </div>
    
    <div class="flex-1 py-2">
        @foreach($types as $type)
            <div class="px-2 mb-1">
                <label class="group block w-full cursor-pointer">
                    <input type="checkbox" wire:model.live="types" value="{{ $type }}" class="sr-only peer">
                    <span class="relative w-full flex justify-center p-2 rounded-md border border-transparent text-zinc-400 hover:text-white hover:bg-background-hover transition-all duration-150 peer-checked:bg-blue-500/10 peer-checked:text-blue-400 peer-checked:border-blue-500/20">
                        @if (isset($typeIcons[$type]))
                            {!! $typeIcons[$type] !!}
                        @else
                            {{ substr($type, 0, 2) }}
                        @endif
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-blue-500 transition-opacity duration-150 opacity-0 peer-checked:opacity-100"></span>
                        <span class="fixed left-[65px] px-2 py-1 bg-background-tertiary border border-border rounded text-xs text-white whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[100] pointer-events-none" style="margin-top: -2px;">
                            {{ Str::title($type) }}
                        </span>
                    </span>
                </label>
            </div>
        @endforeach
        
        <div class="mx-3 my-2 border-t border-border"></div>
        
        @foreach($providers as $provider)
            <div class="px-2 mb-1">
                <label class="group block w-full cursor-pointer">
                    <input type="checkbox" wire:model.live="providers" value="{{ $provider }}" class="sr-only peer">
                    <span class="relative w-full flex justify-center p-2 rounded-md border border-transparent text-xs font-medium text-zinc-400 hover:text-white hover:bg-background-hover transition-all duration-150 peer-checked:bg-blue-500/10 peer-checked:text-blue-400 peer-checked:border-blue-500/20">
                        @if (isset($providerCodes[$provider]))
                            {{ $providerCodes[$provider] }}
                        @else
                            {{ substr($provider, 0, 2) }}
                        @endif
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-blue-500 transition-opacity duration-150 opacity-0 peer-checked:opacity-100"></span>
                        <span class="fixed left-[65px] px-2 py-1 bg-background-tertiary border border-border rounded text-xs text-white whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[100] pointer-events-none" style="margin-top: -2px;">{{ $provider }}</span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
</aside>