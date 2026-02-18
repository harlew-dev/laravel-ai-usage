<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Models\TokenUsage;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Gateway\Gateway;
use Laravel\Ai\Contracts\Providers\AudioProvider;
use Laravel\Ai\Contracts\Providers\EmbeddingProvider;
use Laravel\Ai\Contracts\Providers\ImageProvider;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\AgentStreamed;
use Laravel\Ai\Events\AudioGenerated;
use Laravel\Ai\Events\EmbeddingsGenerated;
use Laravel\Ai\Events\ImageGenerated;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Prompts\AudioPrompt;
use Laravel\Ai\Prompts\EmbeddingsPrompt;
use Laravel\Ai\Prompts\ImagePrompt;
use Laravel\Ai\Providers\Provider;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\AudioResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
use Laravel\Ai\Responses\EmbeddingsResponse;
use Laravel\Ai\Responses\ImageResponse;

if (! function_exists('fakeProvider')) {
    /**
     * @param  class-string  $contract
     */
    function fakeProvider(string $contract, string $name = 'openai'): Provider
    {
        $gateway = \Mockery::mock(Gateway::class);
        $dispatcher = \Mockery::mock(Dispatcher::class);

        return \Mockery::mock(
            Provider::class.', '.$contract,
            [$gateway, ['name' => $name, 'driver' => $name, 'key' => 'test-key'], $dispatcher]
        )->makePartial();
    }
}

if (! function_exists('fakeTokenUsage')) {
    function fakeTokenUsage(array $attributes = []): TokenUsage
    {
        return TokenUsage::query()->forceCreate(array_merge([
            'invocation_id' => 'invocation-'.str()->random(8),
            'type' => 'text',
            'agent' => 'Tests\\Agent',
            'provider' => 'openai',
            'model' => 'gpt-4.1-mini',
            'input_tokens' => 10,
            'output_tokens' => 5,
            'cache_write_tokens' => 2,
            'cache_read_tokens' => 1,
            'reasoning_tokens' => 3,
        ], $attributes));
    }
}

if (! function_exists('makeAgentPromptedEvent')) {
    function makeAgentPromptedEvent(bool $streamed = false): AgentPrompted|AgentStreamed
    {
        $provider = fakeProvider(TextProvider::class, 'anthropic');
        $agent = \Mockery::mock(Agent::class);

        $prompt = new AgentPrompt(
            agent: $agent,
            prompt: 'Summarize this package',
            attachments: [],
            provider: $provider,
            model: 'claude-sonnet-4-5',
        );

        $response = new AgentResponse(
            invocationId: 'agent-invocation-1',
            text: 'Done',
            usage: new Usage(
                promptTokens: 100,
                completionTokens: 50,
                cacheWriteInputTokens: 10,
                cacheReadInputTokens: 5,
                reasoningTokens: 2,
            ),
            meta: new Meta(provider: 'anthropic', model: 'claude-sonnet-4-5'),
        );

        if ($streamed) {
            return new AgentStreamed(
                invocationId: 'agent-invocation-1',
                prompt: $prompt,
                response: $response,
            );
        }

        return new AgentPrompted(
            invocationId: 'agent-invocation-1',
            prompt: $prompt,
            response: $response,
        );
    }
}

if (! function_exists('makeImageGeneratedEvent')) {
    function makeImageGeneratedEvent(): ImageGenerated
    {
        $provider = fakeProvider(ImageProvider::class, 'openai');

        return new ImageGenerated(
            invocationId: 'image-invocation-1',
            provider: $provider,
            model: 'gpt-image-1',
            prompt: new ImagePrompt(
                prompt: 'Draw a mountain sunrise',
                attachments: [],
                size: '1:1',
                quality: 'high',
                provider: $provider,
                model: 'gpt-image-1',
            ),
            response: new ImageResponse(
                images: collect(),
                usage: new Usage(
                    promptTokens: 40,
                    completionTokens: 0,
                    cacheWriteInputTokens: 3,
                    cacheReadInputTokens: 2,
                    reasoningTokens: 0,
                ),
                meta: new Meta(provider: 'openai', model: 'gpt-image-1'),
            ),
        );
    }
}

if (! function_exists('makeEmbeddingsGeneratedEvent')) {
    function makeEmbeddingsGeneratedEvent(): EmbeddingsGenerated
    {
        $provider = fakeProvider(EmbeddingProvider::class, 'openai');

        return new EmbeddingsGenerated(
            invocationId: 'embedding-invocation-1',
            provider: $provider,
            model: 'text-embedding-3-small',
            prompt: new EmbeddingsPrompt(
                inputs: ['Laravel package testing'],
                dimensions: 1536,
                provider: $provider,
                model: 'text-embedding-3-small',
            ),
            response: new EmbeddingsResponse(
                embeddings: [[0.1, 0.2, 0.3]],
                tokens: 88,
                meta: new Meta(provider: 'openai', model: 'text-embedding-3-small'),
            ),
        );
    }
}

if (! function_exists('makeAudioGeneratedEvent')) {
    function makeAudioGeneratedEvent(): AudioGenerated
    {
        $provider = fakeProvider(AudioProvider::class, 'openai');

        return new AudioGenerated(
            invocationId: 'audio-invocation-1',
            provider: $provider,
            model: 'gpt-4o-mini-tts',
            prompt: new AudioPrompt(
                text: 'Hello from tests',
                voice: 'default-female',
                instructions: null,
                provider: $provider,
                model: 'gpt-4o-mini-tts',
            ),
            response: new AudioResponse(
                audio: base64_encode('audio-bytes'),
                meta: new Meta(provider: 'openai', model: 'gpt-4o-mini-tts'),
                mime: 'audio/mpeg',
            ),
        );
    }
}
