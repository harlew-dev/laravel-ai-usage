<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Tests\Support\PackageDisabledTestCase;
use Laravel\Ai\Events\AgentPrompted;

uses(PackageDisabledTestCase::class);

it('does not register the dashboard route when the package is disabled', function (): void {
    expect(app('router')->getRoutes()->getByName('ai.usage'))->toBeNull();
});

it('does not register event listeners when the package is disabled', function (): void {
    expect(app('events')->hasListeners(AgentPrompted::class))->toBeFalse();
});
