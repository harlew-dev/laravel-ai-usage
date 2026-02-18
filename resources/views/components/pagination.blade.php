@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-3">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-600">
                    {{ __('Previous') }}
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-300 transition-colors duration-150 hover:bg-background-hover hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ __('Previous') }}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-300 transition-colors duration-150 hover:bg-background-hover hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ __('Next') }}
                </button>
            @else
                <span class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-600">
                    {{ __('Next') }}
                </span>
            @endif
        </div>

        <div class="hidden w-full items-center justify-between sm:flex">
            <p class="text-xs text-zinc-500">
                {{ __('Showing') }}
                <span class="font-medium text-zinc-300">{{ $paginator->firstItem() }}</span>
                {{ __('to') }}
                <span class="font-medium text-zinc-300">{{ $paginator->lastItem() }}</span>
                {{ __('of') }}
                <span class="font-medium text-zinc-300">{{ $paginator->total() }}</span>
                {{ __('results') }}
            </p>

            <div class="inline-flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-600">
                        {{ __('Previous') }}
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        wire:loading.attr="disabled"
                        class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-300 transition-colors duration-150 hover:bg-background-hover hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ __('Previous') }}
                    </button>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-border bg-background-tertiary px-2 text-sm text-zinc-500">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-blue-500/30 bg-blue-500/20 px-3 text-sm font-medium text-blue-400">
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    wire:loading.attr="disabled"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-300 transition-colors duration-150 hover:bg-background-hover hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        wire:loading.attr="disabled"
                        class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-300 transition-colors duration-150 hover:bg-background-hover hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ __('Next') }}
                    </button>
                @else
                    <span class="inline-flex h-9 items-center rounded-md border border-border bg-background-tertiary px-3 text-sm text-zinc-600">
                        {{ __('Next') }}
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
