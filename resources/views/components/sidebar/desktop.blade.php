@props([
    'types',
    'providers',
])

<aside x-show="!collapsed"
    x-transition:enter="transition-opacity duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed left-0 top-0 bottom-0 bg-background-secondary border-r border-border z-50 lg:z-30 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] overflow-hidden w-[280px] h-full flex-col" x-cloak>
        
    <div class="p-2 border-b border-border flex items-center gap-3">
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500">
                <path d="m21 21-4.34-4.34"></path>
                <circle cx="11" cy="11" r="8"></circle>
            </svg>
            <input wire:model.live="filter" class="h-9 min-w-0 rounded-md border px-3 shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] w-full pl-9 pr-4 py-2 bg-background-tertiary border-border text-white placeholder:text-zinc-600 focus:border-accent focus:ring-1 focus:ring-accent text-sm" placeholder="Filter.." type="text">
        </div>
        <button @click="collapsed = true" class="p-2 rounded-md bg-background-tertiary border border-border text-zinc-400 hover:text-white hover:bg-background-hover transition-all duration-150 flex-shrink-0" aria-label="Collapse sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M3 19V5"></path>
                <path d="m13 6-6 6 6 6"></path>
                <path d="M7 12h14"></path>
            </svg>
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto">
        <div class="px-2 py-3">
            <h3 class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-2">Model Type</h3>
            <div class="space-y-1">
                @foreach($types as $type)
                    <label class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-all duration-150 text-zinc-400 hover:bg-background-hover hover:text-white text-left cursor-pointer">
                        <input type="checkbox" wire:model.live="types" value="{{ $type }}" class="sr-only peer">

                        <span class="w-4 h-4 rounded border flex items-center justify-center transition-all duration-150 border-zinc-600 text-transparent peer-checked:bg-blue-500/10 peer-checked:text-blue-400 peer-checked:border-blue-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>

                        <span class="capitalize">{{ $type }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        
        <div class="px-2 py-3 border-t border-border">
            <h3 class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-2">LLM Providers</h3>
            <div class="space-y-1">
                @foreach($providers as $provider)
                    <label class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-all duration-150 text-zinc-400 hover:bg-background-hover hover:text-white text-left cursor-pointer">
                        <input type="checkbox" wire:model.live="providers" value="{{ $provider }}" class="sr-only peer">

                        <span class="w-4 h-4 rounded border flex items-center justify-center transition-all duration-150 border-zinc-600 text-transparent peer-checked:bg-blue-500/10 peer-checked:text-blue-400 peer-checked:border-blue-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="M20 6 9 17l-5-5"/></svg>
                        </span>

                        <span>{{ Str::title($provider) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</aside>