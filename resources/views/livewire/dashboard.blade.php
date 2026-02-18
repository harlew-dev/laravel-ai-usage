@php
// Create a deterministic key based on all filter values that should trigger refresh
$filterKey = $period . '-' . md5(serialize($types) . serialize($providers) . $filter);
@endphp

<div x-data="{ collapsed: true }">
    <div>
        <x-ai-usage::sidebar.mobile :providers="$this->providers()" :types="$this->types()" />
        <x-ai-usage::sidebar.desktop :providers="$this->providers()" :types="$this->types()" />
    </div>
    
    {{-- Main content starts collapsed to avoid Alpine init flicker --}}
    <main class="transition-all duration-200 ease-[cubic-bezier(0.4,0,0.2,1)]" x-bind:class="collapsed ? '' : 'lg:ml-[220px]'">
        <div class="p-4 lg:p-5 lg:ml-[60px]">
            <div class="flex flex-col sm:flex-row gap-3 mb-4 items-start sm:items-center justify-between">
                <div class="flex gap-3 items-center">
                    {{-- Mobile-only sidebar toggle --}}
                    <button @click="collapsed = false" class="lg:hidden p-2 rounded-md bg-background-tertiary border border-border text-zinc-400 hover:text-white hover:bg-background-hover transition-all duration-150" aria-label="Open sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M9 3v18"></path></svg>
                    </button>
                    <h2 class="text-xl font-semibold text-white">Usage</h2>
                    <x-ai-usage::live-badge tooltip="This data updates every 30 seconds" />
                </div>
                
                {{-- Period selector --}}
                <div class="flex items-center gap-2">
                    <select wire:model.live="period" id="period" class="bg-background-tertiary border border-border rounded-md pl-3 pr-8 py-1.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        @foreach($this->getPeriodOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <livewire:ai-usage::usage wire:key="usage-{{ $filterKey }}" :period="$period" :types="$types" :providers="$providers" :filter="$filter" />
            </div>
            
            <footer class="mt-8 py-6 border-t border-border">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-zinc-500">Based on your <code class="px-1.5 py-0.5 bg-background-tertiary rounded text-zinc-400">token_usages</code> database schema</p>
                </div>
            </footer>
        </div>
    </main>
</div>
