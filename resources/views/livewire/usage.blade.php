<div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <x-ai-usage::card label="Total Tokens" value="{{ $this->totalTokens() }}" />
        <x-ai-usage::card label="Input Tokens" value="{{ $this->inputTokens() }}" />
        <x-ai-usage::card label="Output Tokens" value="{{ $this->outputTokens() }}" />
        <x-ai-usage::card label="Avg. Tokens/Request" value="{{ $this->avgTokensPerRequest() }}" />
        <x-ai-usage::card label="Top Model" value="{{ $this->topModel() }}" />
        <x-ai-usage::card label="Top Agent" value="{{ $this->topAgent() }}" />
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="col-span-full">
            <div class="bg-background-secondary border border-border rounded-lg p-4">
                <div class="flex flex-col sm:flex-row gap-3 mb-4 items-start sm:items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-300">Tokens</h3>

                    <div class="flex items-center gap-1">
                        <x-ai-usage::tab
                            wire:click="$set('usageChartType', 'total')"
                            :active="$usageChartType === 'total'"
                        >
                            Total
                        </x-ai-usage::tab>
                        <x-ai-usage::tab
                            wire:click="$set('usageChartType', 'per_type')"
                            :active="$usageChartType === 'per_type'"
                        >
                            Types
                        </x-ai-usage::tab>
                    </div>
                </div>

                <div class="relative h-64 w-full"
                    wire:key="usage-chart-{{ $usageChartType }}"
                    wire:ignore
                    x-data="usageChartComponent()"
                    x-init="mount({
                        labels: @js($this->usageChartData['labels']),
                        totalData: @js($this->usageChartData['total']),
                        typeData: @js($this->usageChartData['types']),
                        usageChartType: '{{ $usageChartType }}'
                    })"
                >
                    <canvas x-ref="canvas" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="col-span-full">
            <div class="bg-background-secondary border border-border rounded-lg p-4">
                <div class="flex flex-col sm:flex-row gap-3 mb-4 items-start sm:items-center justify-between">
                    <h3 class="text-sm font-medium text-zinc-300">Requests</h3>

                    <div class="flex items-center gap-1">
                        <x-ai-usage::tab
                            wire:click="$set('requestsChartType', 'total')"
                            :active="$requestsChartType === 'total'"
                        >
                            Total
                        </x-ai-usage::tab>
                        <x-ai-usage::tab
                            wire:click="$set('requestsChartType', 'per_type')"
                            :active="$requestsChartType === 'per_type'"
                        >
                            Types
                        </x-ai-usage::tab>
                    </div>
                </div>

                <div class="relative h-64 w-full"
                    wire:key="requests-chart-{{ $requestsChartType }}"
                    wire:ignore
                    x-data="requestsChartComponent()"
                    x-init="mount({
                        labels: @js($this->requestsChartData['labels']),
                        totalData: @js($this->requestsChartData['total']),
                        typeData: @js($this->requestsChartData['types']),
                        requestsChartType: '{{ $requestsChartType }}'
                    })"
                >
                    <canvas x-ref="canvas" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div>
        <x-ai-usage::table.root>
            <x-ai-usage::table.columns class="bg-background-secondary">
                <x-ai-usage::table.column>Provider</x-ai-usage::table.column>
                <x-ai-usage::table.column>Model</x-ai-usage::table.column>
                <x-ai-usage::table.column>Type</x-ai-usage::table.column>
                <x-ai-usage::table.column>Agent</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Input Tokens</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Output Tokens</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Cache Write</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Cache Read</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Reasoning</x-ai-usage::table.column>
                <x-ai-usage::table.column align="right">Total Tokens</x-ai-usage::table.column>
                <x-ai-usage::table.column>Date</x-ai-usage::table.column>
            </x-ai-usage::table.columns>
            <x-ai-usage::table.rows>
                @forelse($this->tokenUsages() as $tokenUsage)
                    <x-ai-usage::table.row :wire:key="$tokenUsage->id">
                        <x-ai-usage::table.cell variant="code">{{ $tokenUsage->provider }}</x-ai-usage::table.cell>
                        <x-ai-usage::table.cell variant="text">{{ $tokenUsage->model }}</x-ai-usage::table.cell>
                        <x-ai-usage::table.cell>
                            <x-ai-usage::badge :type="$tokenUsage->type" />
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell variant="code">
                            @if ($tokenUsage->agent)
                                <x-ai-usage::badge :type="$tokenUsage->agent" />
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="code">
                            @if ($tokenUsage->input_tokens !== null)
                                {{ number_format($tokenUsage->input_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="code">
                            @if ($tokenUsage->output_tokens !== null)
                                {{ number_format($tokenUsage->output_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="code">
                            @if ($tokenUsage->cache_write_tokens !== null)
                                {{ number_format($tokenUsage->cache_write_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="code">
                            @if ($tokenUsage->cache_read_tokens !== null)
                                {{ number_format($tokenUsage->cache_read_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="code">
                            @if ($tokenUsage->reasoning_tokens !== null)
                                {{ number_format($tokenUsage->reasoning_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell align="right" variant="title">
                            @if ($tokenUsage->total_tokens !== null)
                                {{ number_format($tokenUsage->total_tokens) }}
                            @else
                                -
                            @endif
                        </x-ai-usage::table.cell>
                        <x-ai-usage::table.cell class="text-zinc-500">
                            {{ $tokenUsage->created_at?->format('M d, Y') }}
                        </x-ai-usage::table.cell>
                    </x-ai-usage::table.row>
                @empty
                    <x-ai-usage::table.row>
                        <x-ai-usage::table.cell colspan="11">
                            <div class="text-center text-zinc-500">No data found</div>
                        </x-ai-usage::table.cell>
                    </x-ai-usage::table.row>
                @endforelse
            </x-ai-usage::table.rows>
        </x-ai-usage::table.root>

        <div class="mt-4">
            {{ $this->tokenUsages()->links() }}
        </div>
    </div>
</div>
