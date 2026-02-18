<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Tests\Support\ListenersDisabledTestCase;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\AgentStreamed;
use Laravel\Ai\Events\AudioGenerated;
use Laravel\Ai\Events\EmbeddingsGenerated;
use Laravel\Ai\Events\ImageGenerated;

uses(ListenersDisabledTestCase::class);

it('does not register ai usage listeners when listener config is disabled', function (): void {
    $dispatcher = app('events');

    expect($dispatcher->hasListeners(AgentPrompted::class))->toBeFalse()
        ->and($dispatcher->hasListeners(AgentStreamed::class))->toBeFalse()
        ->and($dispatcher->hasListeners(ImageGenerated::class))->toBeFalse()
        ->and($dispatcher->hasListeners(EmbeddingsGenerated::class))->toBeFalse()
        ->and($dispatcher->hasListeners(AudioGenerated::class))->toBeFalse();
});
