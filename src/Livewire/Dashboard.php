<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Livewire;

use HarlewDev\AiUsage\Concerns\DashboardAttributes;
use HarlewDev\AiUsage\Models\TokenUsage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('ai-usage::layouts.base')]
class Dashboard extends Component
{
    use DashboardAttributes;

    public function render(): View
    {
        return view('ai-usage::livewire.dashboard');
    }

    #[Computed]
    public function providers()
    {
        $providers = config('ai.providers');
        $providerNames = array_keys($providers);

        return collect($providerNames)
            ->merge(TokenUsage::distinct()->pluck('provider'))
            ->unique()
            ->values();
    }

    #[Computed]
    public function types()
    {
        return collect(['text', 'embedding', 'image', 'audio'])
            ->merge(TokenUsage::distinct()->pluck('type'))
            ->unique()
            ->values();
    }
}
