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

    public string $usageChartType = 'tokens';

    public string $requestsChartType = 'total';

    public function render(): View
    {
        return view('ai-usage::livewire.usage');
    }

    #[Computed]
    public function totalCost(): string
    {
        $totalCost = $this->getFilteredQuery()->sum('total_cost') ?? 0;

        return number_format($totalCost / 100, 2);
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
    public function modelsUsed(): string
    {
        $modelsUsed = $this->getFilteredQuery()->distinct('model')->count('model');

        return number_format($modelsUsed);
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
        $results = $this->buildDateGroupedQuery()
            ->select([
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('SUM(total_cost) as total_cost'),
                DB::raw('SUM(total_tokens) as total_tokens'),
            ])
            ->groupBy('date_group')
            ->orderBy('date_group')
            ->get();

        $labels = [];
        $costData = [];
        $tokenData = [];

        foreach ($this->getDateRange() as $date) {
            $formattedDate = $date->format($dateFormat);
            $dbDate = $date->format($dbDateFormat);
            $record = $results->firstWhere('date_group', $dbDate);

            $labels[] = $formattedDate;
            $costData[] = $record ? round($record->total_cost / 100, 2) : 0;
            $tokenData[] = $record ? (int) $record->total_tokens : 0;
        }

        return [
            'labels' => $labels,
            'cost' => $costData,
            'tokens' => $tokenData,
        ];
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
            'models' => $this->getModelData($baseQuery, $dates, $dbDateFormat),
        ];
    }

    /**
     * @param  array<int, Carbon>  $dates
     * @return array<string, array<int, int>>
     */
    private function getModelData(Builder $baseQuery, array $dates, string $dbDateFormat): array
    {
        $topModels = (clone $baseQuery)
            ->select('model', DB::raw('COUNT(*) as total_count'))
            ->groupBy('model')
            ->orderByDesc('total_count')
            ->limit(5)
            ->pluck('model')
            ->toArray();

        if (empty($topModels)) {
            return [];
        }

        $modelResults = (clone $baseQuery)
            ->whereIn('model', $topModels)
            ->select([
                'model',
                DB::raw($this->getDateGroupExpression().' as date_group'),
                DB::raw('COUNT(*) as request_count'),
            ])
            ->groupBy('model', 'date_group')
            ->orderBy('model')
            ->orderBy('date_group')
            ->get();

        $modelData = [];
        foreach ($topModels as $model) {
            $modelData[$model] = array_fill(0, count($dates), 0);
        }

        foreach ($modelResults as $row) {
            $dateIndex = $this->getDateIndex($row->date_group, $dates, $dbDateFormat);
            if ($dateIndex !== null && isset($modelData[$row->model])) {
                $modelData[$row->model][$dateIndex] = (int) $row->request_count;
            }
        }

        return $modelData;
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
