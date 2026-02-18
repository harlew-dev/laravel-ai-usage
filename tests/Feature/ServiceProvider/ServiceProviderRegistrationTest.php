<?php

declare(strict_types=1);

use HarlewDev\AiUsage\AiUsage;
use HarlewDev\AiUsage\Tests\TestCase;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\AgentStreamed;
use Laravel\Ai\Events\AudioGenerated;
use Laravel\Ai\Events\EmbeddingsGenerated;
use Laravel\Ai\Events\ImageGenerated;

uses(TestCase::class);

it('registers the ai usage singleton', function (): void {
    expect(app(AiUsage::class))->toBeInstanceOf(AiUsage::class);
});

it('registers ai event listeners when the package is enabled', function (): void {
    $dispatcher = app('events');

    expect($dispatcher->hasListeners(AgentPrompted::class))->toBeTrue()
        ->and($dispatcher->hasListeners(AgentStreamed::class))->toBeTrue()
        ->and($dispatcher->hasListeners(ImageGenerated::class))->toBeTrue()
        ->and($dispatcher->hasListeners(EmbeddingsGenerated::class))->toBeTrue()
        ->and($dispatcher->hasListeners(AudioGenerated::class))->toBeTrue();
});
