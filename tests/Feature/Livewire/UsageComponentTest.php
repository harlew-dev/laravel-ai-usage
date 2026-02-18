<?php

declare(strict_types=1);

use Carbon\Carbon;
use HarlewDev\AiUsage\Livewire\Usage;
use HarlewDev\AiUsage\Tests\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

afterEach(function (): void {
    Carbon::setTestNow();
});

it('calculates aggregate token and request metrics', function (): void {
    fakeTokenUsage([
        'input_tokens' => 100,
        'output_tokens' => 40,
        'cache_write_tokens' => 5,
        'cache_read_tokens' => 3,
        'reasoning_tokens' => 2,
    ]);

    fakeTokenUsage([
        'input_tokens' => 50,
        'output_tokens' => 10,
        'cache_write_tokens' => 0,
        'cache_read_tokens' => 0,
        'reasoning_tokens' => 0,
    ]);

    $component = Livewire::test(Usage::class, ['period' => 'all']);

    expect($component->get('totalTokens'))->toBe('210')
        ->and($component->get('inputTokens'))->toBe('150')
        ->and($component->get('outputTokens'))->toBe('50')
        ->and($component->get('totalRequests'))->toBe('2');
});

it('applies type and provider filters to usage metrics', function (): void {
    fakeTokenUsage([
        'type' => 'image',
        'provider' => 'openai',
        'input_tokens' => 20,
    ]);

    fakeTokenUsage([
        'type' => 'text',
        'provider' => 'anthropic',
        'input_tokens' => 99,
    ]);

    $component = Livewire::test(Usage::class, [
        'period' => 'all',
        'types' => ['image'],
        'providers' => ['openai'],
    ]);

    expect($component->get('totalRequests'))->toBe('1')
        ->and($component->get('inputTokens'))->toBe('20');
});

it('applies a case insensitive search filter to provider model and agent columns', function (): void {
    fakeTokenUsage([
        'provider' => 'OpenAI',
        'model' => 'gpt-4o',
        'agent' => 'App\\Agents\\SupportAgent',
    ]);

    fakeTokenUsage([
        'provider' => 'Anthropic',
        'model' => 'claude-sonnet',
        'agent' => 'App\\Agents\\BillingAgent',
    ]);

    $component = Livewire::test(Usage::class, [
        'period' => 'all',
        'filter' => 'supportagent',
    ]);

    expect($component->get('totalRequests'))->toBe('1');
});

it('builds usage chart data with consistent labels totals and type series', function (): void {
    Carbon::setTestNow('2026-02-18 12:00:00');

    fakeTokenUsage([
        'type' => 'text',
        'input_tokens' => 30,
        'output_tokens' => 0,
        'cache_write_tokens' => 0,
        'cache_read_tokens' => 0,
        'reasoning_tokens' => 0,
        'created_at' => Carbon::now()->subDays(2),
        'updated_at' => Carbon::now()->subDays(2),
    ]);

    fakeTokenUsage([
        'type' => 'image',
        'input_tokens' => 20,
        'output_tokens' => 0,
        'cache_write_tokens' => 0,
        'cache_read_tokens' => 0,
        'reasoning_tokens' => 0,
        'created_at' => Carbon::now()->subDay(),
        'updated_at' => Carbon::now()->subDay(),
    ]);

    fakeTokenUsage([
        'type' => 'audio',
        'input_tokens' => 90,
        'output_tokens' => 0,
        'cache_write_tokens' => 0,
        'cache_read_tokens' => 0,
        'reasoning_tokens' => 0,
        'created_at' => Carbon::now()->subDays(10),
        'updated_at' => Carbon::now()->subDays(10),
    ]);

    $chartData = Livewire::test(Usage::class, ['period' => '7d'])->get('usageChartData');

    expect($chartData)->toHaveKeys(['labels', 'total', 'types'])
        ->and($chartData['labels'])->toBeArray()
        ->and($chartData['total'])->toBeArray()
        ->and(count($chartData['labels']))->toBe(count($chartData['total']))
        ->and(array_sum($chartData['total']))->toBe(50)
        ->and($chartData['types'])->toHaveKeys(['text', 'image']);
});

it('builds requests chart data with total request counts by period', function (): void {
    Carbon::setTestNow('2026-02-18 12:00:00');

    fakeTokenUsage([
        'type' => 'text',
        'created_at' => Carbon::now()->subDays(2),
        'updated_at' => Carbon::now()->subDays(2),
    ]);

    fakeTokenUsage([
        'type' => 'image',
        'created_at' => Carbon::now()->subDay(),
        'updated_at' => Carbon::now()->subDay(),
    ]);

    fakeTokenUsage([
        'type' => 'audio',
        'created_at' => Carbon::now()->subDays(10),
        'updated_at' => Carbon::now()->subDays(10),
    ]);

    $chartData = Livewire::test(Usage::class, ['period' => '7d'])->get('requestsChartData');

    expect($chartData)->toHaveKeys(['labels', 'total', 'types'])
        ->and(count($chartData['labels']))->toBe(count($chartData['total']))
        ->and(array_sum($chartData['total']))->toBe(2)
        ->and($chartData['types'])->toHaveKeys(['text', 'image']);
});

it('paginates token usage rows in pages of twenty', function (): void {
    foreach (range(1, 25) as $index) {
        fakeTokenUsage(['invocation_id' => 'invocation-'.$index]);
    }

    $paginator = Livewire::test(Usage::class, ['period' => 'all'])->get('tokenUsages');

    expect($paginator->total())->toBe(25)
        ->and($paginator->count())->toBe(20)
        ->and($paginator->perPage())->toBe(20);
});
