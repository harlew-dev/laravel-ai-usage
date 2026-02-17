<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Listeners;

use HarlewDev\AiUsage\Models\TokenUsage;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\AgentStreamed;
use Laravel\Ai\Providers\Provider;

class AgentUsage
{
    public function handle(AgentPrompted|AgentStreamed $event): void
    {
        $provider = $event->prompt->provider;

        $provider = match (true) {
            $provider instanceof Provider => $provider->name(),
            default => get_class($provider),
        };

        TokenUsage::create([
            'invocation_id' => $event->invocationId,
            'type' => 'text',
            'agent' => get_class($event->prompt->agent),
            'provider' => $provider,
            'model' => $event->prompt->model,
            'input_tokens' => $event->response->usage->promptTokens,
            'output_tokens' => $event->response->usage->completionTokens,
            'cache_write_input_tokens' => $event->response->usage->cacheWriteInputTokens,
            'cache_read_input_tokens' => $event->response->usage->cacheReadInputTokens,
            'reasoning_tokens' => $event->response->usage->reasoningTokens,
        ]);
    }
}
