<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Livewire\Dashboard;
use HarlewDev\AiUsage\Tests\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

it('renders the dashboard livewire component', function (): void {
    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Usage')
        ->assertSee('24 hours')
        ->assertSee('All time');
});

it('builds provider options from config and persisted usage records', function (): void {
    fakeTokenUsage(['provider' => 'custom-provider']);

    $providers = Livewire::test(Dashboard::class)->instance()->providers();

    expect($providers)->toContain('openai')
        ->and($providers)->toContain('anthropic')
        ->and($providers)->toContain('custom-provider');
});

it('builds type options from defaults and persisted usage records', function (): void {
    fakeTokenUsage(['type' => 'rerank']);

    $types = Livewire::test(Dashboard::class)->instance()->types();

    expect($types)->toContain('text')
        ->and($types)->toContain('embedding')
        ->and($types)->toContain('image')
        ->and($types)->toContain('audio')
        ->and($types)->toContain('rerank');
});
