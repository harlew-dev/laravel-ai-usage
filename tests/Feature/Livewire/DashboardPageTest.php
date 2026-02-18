<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Livewire\Dashboard;
use HarlewDev\AiUsage\Tests\Support\LocalEnvironmentTestCase;

uses(LocalEnvironmentTestCase::class);

it('loads the dashboard route with the livewire component', function (): void {
    $this->get(route('ai.usage'))
        ->assertOk()
        ->assertSeeLivewire(Dashboard::class)
        ->assertSee('Usage');
});
