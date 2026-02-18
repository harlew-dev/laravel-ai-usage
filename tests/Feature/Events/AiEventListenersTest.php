<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Models\TokenUsage;
use HarlewDev\AiUsage\Tests\TestCase;
use Laravel\Ai\Events\AgentStreamed;

uses(TestCase::class);

it('persists token usage for prompted agent responses', function (): void {
    $event = makeAgentPromptedEvent();

    event($event);

    $usage = TokenUsage::query()->first();

    expect($usage)->not->toBeNull()
        ->and($usage->invocation_id)->toBe('agent-invocation-1')
        ->and($usage->type)->toBe('text')
        ->and($usage->provider)->toBe('anthropic')
        ->and($usage->model)->toBe('claude-sonnet-4-5')
        ->and($usage->agent)->toBe(get_class($event->prompt->agent))
        ->and($usage->input_tokens)->toBe(100)
        ->and($usage->output_tokens)->toBe(50)
        ->and($usage->cache_write_tokens)->toBe(10)
        ->and($usage->cache_read_tokens)->toBe(5)
        ->and($usage->reasoning_tokens)->toBe(2)
        ->and($usage->total_tokens)->toBe(167);
});

it('persists token usage for streamed agent responses', function (): void {
    $event = makeAgentPromptedEvent(streamed: true);

    expect($event)->toBeInstanceOf(AgentStreamed::class);

    event($event);

    expect(TokenUsage::query()->where('type', 'text')->count())->toBe(1);
});

it('persists token usage for generated images', function (): void {
    event(makeImageGeneratedEvent());

    $usage = TokenUsage::query()->first();

    expect($usage)->not->toBeNull()
        ->and($usage->invocation_id)->toBe('image-invocation-1')
        ->and($usage->type)->toBe('image')
        ->and($usage->provider)->toBe('openai')
        ->and($usage->model)->toBe('gpt-image-1')
        ->and($usage->input_tokens)->toBe(40)
        ->and($usage->cache_write_tokens)->toBe(3)
        ->and($usage->cache_read_tokens)->toBe(2)
        ->and($usage->total_tokens)->toBe(45);
});

it('persists token usage for generated embeddings', function (): void {
    event(makeEmbeddingsGeneratedEvent());

    $usage = TokenUsage::query()->first();

    expect($usage)->not->toBeNull()
        ->and($usage->invocation_id)->toBe('embedding-invocation-1')
        ->and($usage->type)->toBe('embedding')
        ->and($usage->provider)->toBe('openai')
        ->and($usage->model)->toBe('text-embedding-3-small')
        ->and($usage->input_tokens)->toBe(88)
        ->and($usage->total_tokens)->toBe(88);
});

it('persists token usage for generated audio', function (): void {
    event(makeAudioGeneratedEvent());

    $usage = TokenUsage::query()->first();

    expect($usage)->not->toBeNull()
        ->and($usage->invocation_id)->toBe('audio-invocation-1')
        ->and($usage->type)->toBe('audio')
        ->and($usage->provider)->toBe('openai')
        ->and($usage->model)->toBe('gpt-4o-mini-tts')
        ->and($usage->total_tokens)->toBe(0);
});
