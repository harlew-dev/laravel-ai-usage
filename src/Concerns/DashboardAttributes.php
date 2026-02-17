<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Concerns;

use Carbon\Carbon;
use HarlewDev\AiUsage\Models\TokenUsage;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

trait DashboardAttributes
{
    public string $period = 'today';
    
    public array $types = [];

    public array $providers = [];

    public string $filter = '';

    protected function applyFilter(Builder $query): Builder
    {
        if (empty($this->filter)) {
            return $query;
        }

        $filter = '%' . strtolower($this->filter) . '%';

        return $query->where(function (Builder $q) use ($filter) {
            $q->whereRaw('LOWER(provider) LIKE ?', [$filter])
              ->orWhereRaw('LOWER(model) LIKE ?', [$filter])
              ->orWhereRaw('LOWER(agent) LIKE ?', [$filter]);
        });
    }

    protected function baseQuery(): Builder
    {
        $query = TokenUsage::query();

        // Period filter
        if (method_exists($this, 'getPeriodStartDate')) {
            if ($startDate = $this->getPeriodStartDate()) {
                $query->where('created_at', '>=', $startDate);
            }
        }

        // Type filter
        if (! empty($this->types)) {
            $query->whereIn('type', $this->types);
        }

        // Provider filter
        if (! empty($this->providers)) {
            $query->whereIn('provider', $this->providers);
        }

        // Search filter (provider, model, agent)
        $query = $this->applyFilter($query);

        return $query;
    }

    protected function getFilteredQuery(): Builder
    {
        return $this->baseQuery();
    }
    
    public function getPeriodOptions(): array
    {
        return [
            'today' => '24 hours',
            '7d' => '7 days',
            '14d' => '14 days',
            '1m' => '1 month',
            '1y' => '1 year',
            'all' => 'All time',
        ];
    }

    public function getPeriodStartDate(): ?Carbon
    {
        return match ($this->period) {
            'today' => Carbon::now()->subHours(24),
            '7d' => Carbon::now()->subDays(7)->startOfDay(),
            '14d' => Carbon::now()->subDays(14)->startOfDay(),
            '1m' => Carbon::now()->subMonth()->startOfDay(),
            '1y' => Carbon::now()->subYear()->startOfDay(),
            'all' => null,
            default => Carbon::now()->subDays(7)->startOfDay(),
        };
    }
}
