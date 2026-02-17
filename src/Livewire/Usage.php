<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Livewire;

use Carbon\Carbon;
use HarlewDev\AiUsage\Concerns\DashboardAttributes;
use HarlewDev\AiUsage\Models\TokenUsage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Usage extends Component
{
    use DashboardAttributes, WithPagination;

    public string $usageChartType = 'total';

    public string $requestsChartType = 'total';

    public function render(): View
    {
        return view('ai-usage::livewire.usage');
    }

    #[Computed]
    public function totalTokens(): string
    {
        $totalTokens = $this->getFilteredQuery()->sum('total_tokens') ?? 0;

        return number_format($totalTokens);
    }

    #[Computed]
    public function totalRequests(): string
    {
        return number_format($this->getFilteredQuery()->count());
    }

    #[Computed]
    public function inputTokens(): string
    {
        $inputTokens = $this->getFilteredQuery()->sum('input_tokens') ?? 0;

        return number_format($inputTokens);
    }

    #[Computed]
    public function outputTokens(): string
    {
        $outputTokens = $this->getFilteredQuery()->sum('output_tokens') ?? 0;

        return number_format($outputTokens);
    }

    #[Computed]
    public function avgTokensPerRequest(): string
    {
        $totalRequests = $this->getFilteredQuery()->count();
        $totalTokens = $this->getFilteredQuery()->sum('total_tokens') ?? 0;

        if ($totalRequests === 0) {
            return '0';
        }

        return number_format(round($totalTokens / $totalRequests));
    }

    #[Computed]
    public function topModel(): string
    {
        $topModel = $this->getFilteredQuery()
            ->select('model', DB::raw('COUNT(*) as count'))
            ->groupBy('model')
            ->orderByDesc('count')
            ->first();

        return $topModel ? $topModel->model : '-';
    }

    #[Computed]
    public function topAgent(): string
    {
        $topAgent = $this->getFilteredQuery()
            ->whereNotNull('agent')
            ->select('agent', DB::raw('COUNT(*) as count'))
            ->groupBy('agent')
            ->orderByDesc('count')
            ->first();

        if (! $topAgent) {
            return '-';
        }

        // Extract class name from full namespace
        $agentClass = $topAgent->agent;
        $parts = explode('\\', $agentClass);

        return end($parts);
    }

    #[Computed]
    public function tokenUsages()
    {
        return $this->getFilteredQuery()
            ->orderByDesc('created_at')
            ->paginate(20, pageName: 'usagePage');
    }

    #[Computed]
    public function usageChartData(): array
    {
        $dateFormat = $this->getDateFormat();
        $dbDateFormat = $this->getDbDateFormat();
        $dates = $this->getDateRange();

        $labels = [];
        foreach ($dates as $date) {
            $labels[] = $date->format($dateFormat);
        }

        $baseQuery = $this->buildDateGroupedQuery();

        $totalResults = (clone $baseQuery)
            ->select([
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('SUM(total_tokens) as total_tokens'),
            ])
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get()
            ->keyBy('date_group');

        $totalData = [];
        foreach ($dates as $date) {
            $dbDate = $date->format($dbDateFormat);
            $record = $totalResults->get($dbDate);
            $totalData[] = $record ? (int) $record->total_tokens : 0;
        }

        return [
            'labels' => $labels,
            'total' => $totalData,
            'types' => $this->getUsageTypeData($baseQuery, $dates, $dbDateFormat),
        ];
    }

    /**
     * @param  array<int, Carbon>  $dates
     * @return array<string, array<int, int>>
     */
    private function getUsageTypeData(Builder $baseQuery, array $dates, string $dbDateFormat): array
    {
        $types = (clone $baseQuery)
            ->select('type', DB::raw('SUM(total_tokens) as total_tokens'))
            ->groupBy('type')
            ->orderByDesc('total_tokens')
            ->pluck('type')
            ->toArray();

        if (empty($types)) {
            return [];
        }

        $typeResults = (clone $baseQuery)
            ->whereIn('type', $types)
            ->select([
                'type',
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('SUM(total_tokens) as total_tokens'),
            ])
            ->groupBy('type', 'date_group')
            ->orderBy('type')
            ->orderBy('date_group')
            ->get();

        $typeData = [];
        foreach ($types as $type) {
            $typeData[$type] = array_fill(0, count($dates), 0);
        }

        foreach ($typeResults as $row) {
            $dateIndex = $this->getDateIndex($row->date_group, $dates, $dbDateFormat);
            if ($dateIndex !== null && isset($typeData[$row->type])) {
                $typeData[$row->type][$dateIndex] = (int) $row->total_tokens;
            }
        }

        return $typeData;
    }

    #[Computed]
    public function requestsChartData(): array
    {
        $dateFormat = $this->getDateFormat();
        $dbDateFormat = $this->getDbDateFormat();
        $dates = $this->getDateRange();

        $labels = [];
        foreach ($dates as $date) {
            $labels[] = $date->format($dateFormat);
        }

        $baseQuery = $this->buildDateGroupedQuery();

        $totalResults = (clone $baseQuery)
            ->select([
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('COUNT(*) as request_count'),
            ])
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get()
            ->keyBy('date_group');

        $totalData = [];
        foreach ($dates as $date) {
            $dbDate = $date->format($dbDateFormat);
            $record = $totalResults->get($dbDate);
            $totalData[] = $record ? (int) $record->request_count : 0;
        }

        return [
            'labels' => $labels,
            'total' => $totalData,
            'types' => $this->getTypeData($baseQuery, $dates, $dbDateFormat),
        ];
    }

    /**
     * @param  array<int, Carbon>  $dates
     * @return array<string, array<int, int>>
     */
    private function getTypeData(Builder $baseQuery, array $dates, string $dbDateFormat): array
    {
        $types = (clone $baseQuery)
            ->select('type', DB::raw('COUNT(*) as total_count'))
            ->groupBy('type')
            ->orderByDesc('total_count')
            ->pluck('type')
            ->toArray();

        if (empty($types)) {
            return [];
        }

        $typeResults = (clone $baseQuery)
            ->whereIn('type', $types)
            ->select([
                'type',
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('COUNT(*) as request_count'),
            ])
            ->groupBy('type', 'date_group')
            ->orderBy('type')
            ->orderBy('date_group')
            ->get();

        $typeData = [];
        foreach ($types as $type) {
            $typeData[$type] = array_fill(0, count($dates), 0);
        }

        foreach ($typeResults as $row) {
            $dateIndex = $this->getDateIndex($row->date_group, $dates, $dbDateFormat);
            if ($dateIndex !== null && isset($typeData[$row->type])) {
                $typeData[$row->type][$dateIndex] = (int) $row->request_count;
            }
        }

        return $typeData;
    }

    /**
     * @param  array<int, Carbon>  $dates
     */
    private function getDateIndex(string $dateGroup, array $dates, string $dbDateFormat): ?int
    {
        foreach ($dates as $index => $date) {
            if ($date->format($dbDateFormat) === $dateGroup) {
                return $index;
            }
        }

        return null;
    }

    private function buildDateGroupedQuery(): Builder
    {
        $query = TokenUsage::query();

        if ($startDate = $this->getPeriodStartDate()) {
            $query->where('created_at', '>=', $startDate);
        }

        if (! empty($this->types)) {
            $query->whereIn('type', $this->types);
        }

        if (! empty($this->providers)) {
            $query->whereIn('provider', $this->providers);
        }

        // Search filter (provider, model, agent)
        $query = $this->applyFilter($query);

        return $query;
    }

    private function getDateGroupExpression(): string
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'pgsql' => $this->getPostgresDateExpression(),
            'mysql', 'mariadb' => $this->getMysqlDateExpression(),
            'sqlite' => $this->getSqliteDateExpression(),
            default => $this->getMysqlDateExpression(),
        };
    }

    private function getPostgresDateExpression(): string
    {
        $format = match ($this->period) {
            'today' => 'YYYY-MM-DD HH24:00',
            '7d', '14d', '1m' => 'YYYY-MM-DD',
            '1y', 'all' => 'YYYY-MM',
            default => 'YYYY-MM-DD',
        };

        return "TO_CHAR(created_at, '{$format}')";
    }

    private function getMysqlDateExpression(): string
    {
        $format = match ($this->period) {
            'today' => '%Y-%m-%d %H:00',
            '7d', '14d', '1m' => '%Y-%m-%d',
            '1y', 'all' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return "DATE_FORMAT(created_at, '{$format}')";
    }

    private function getSqliteDateExpression(): string
    {
        $format = match ($this->period) {
            'today' => '%Y-%m-%d %H:00',
            '7d', '14d', '1m' => '%Y-%m-%d',
            '1y', 'all' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return "strftime('{$format}', created_at)";
    }

    private function getDateFormat(): string
    {
        return match ($this->period) {
            'today' => 'H:00',
            '7d', '14d', '1m' => 'M d',
            '1y', 'all' => 'M Y',
            default => 'M d',
        };
    }

    private function getDbDateFormat(): string
    {
        return match ($this->period) {
            'today' => 'Y-m-d H:00',
            '7d', '14d', '1m' => 'Y-m-d',
            '1y', 'all' => 'Y-m',
            default => 'Y-m-d',
        };
    }

    /**
     * @return array<int, Carbon>
     */
    private function getDateRange(): array
    {
        $dates = [];
        $startDate = $this->getPeriodStartDate() ?? Carbon::now()->subYear();
        $endDate = Carbon::now();

        $interval = match ($this->period) {
            'today' => 'hour',
            '7d', '14d', '1m' => 'day',
            '1y', 'all' => 'month',
            default => 'day',
        };

        if ($this->period === 'today') {
            // For last 24 hours, generate exactly 24 hourly buckets
            $current = $startDate->copy()->startOf('hour');
            while ($current <= $endDate) {
                $dates[] = $current->copy();
                $current->addHour();
            }
        } else {
            $current = $startDate->copy()->startOf($interval);
            while ($current <= $endDate) {
                $dates[] = $current->copy();
                $current->add(1, $interval);
            }
        }

        return $dates;
    }
}
