<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Http\Middleware\Authorize;
use HarlewDev\AiUsage\Tests\TestCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;

uses(TestCase::class);

it('authorizes the request using the viewAiUsage gate ability', function (): void {
    $gate = \Mockery::mock(Gate::class);
    $gate->shouldReceive('authorize')
        ->once()
        ->with('viewAiUsage');

    $middleware = new Authorize($gate);
    $response = $middleware->handle(new Request, fn (): string => 'next');

    expect($response)->toBe('next');
});

it('throws when the gate denies authorization', function (): void {
    $gate = \Mockery::mock(Gate::class);
    $gate->shouldReceive('authorize')
        ->once()
        ->with('viewAiUsage')
        ->andThrow(new AuthorizationException);

    $middleware = new Authorize($gate);

    expect(fn () => $middleware->handle(new Request, fn (): string => 'next'))
        ->toThrow(AuthorizationException::class);
});
