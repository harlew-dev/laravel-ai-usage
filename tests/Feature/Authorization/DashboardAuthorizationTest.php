<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Http\Middleware\Authorize;
use HarlewDev\AiUsage\Tests\TestCase;
use Illuminate\Support\Facades\Gate;

uses(TestCase::class);

it('denies dashboard access outside the local environment', function (): void {
    $this->get(route('ai.usage'))
        ->assertForbidden();
});

it('registers the dashboard route with authorization middleware', function (): void {
    $route = app('router')->getRoutes()->getByName('ai.usage');

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('web')
        ->and($route->gatherMiddleware())->toContain(Authorize::class);
});

it('registers the viewAiUsage gate ability', function (): void {
    expect(Gate::has('viewAiUsage'))->toBeTrue()
        ->and(Gate::allows('viewAiUsage'))->toBeFalse();
});
