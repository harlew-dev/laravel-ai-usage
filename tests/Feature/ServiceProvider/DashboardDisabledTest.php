<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Tests\Support\DashboardDisabledTestCase;
use Laravel\Ai\Events\AgentPrompted;

uses(DashboardDisabledTestCase::class);

it('does not register the dashboard route when dashboard is disabled', function (): void {
    expect(app('router')->getRoutes()->getByName('ai.usage'))->toBeNull();
});

it('still registers ai event listeners when dashboard is disabled', function (): void {
    expect(app('events')->hasListeners(AgentPrompted::class))->toBeTrue();
});
