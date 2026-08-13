<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Concerns;

use Livewire\Attributes\On;

trait DashboardUpdated
{
    #[On('dashboard-updated')]
    public function dashboardUpdated(array $types, array $providers, string $period): void
    {
        $this->types = $types;
        $this->providers = $providers;
        $this->period = $period;
    }
}