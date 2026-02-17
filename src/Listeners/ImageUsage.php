<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Listeners;

use HarlewDev\AiUsage\Models\TokenUsage;
use Laravel\Ai\Events\ImageGenerated;

class ImageUsage
{
    /**
     * Handle the event.
     */
    public function handle(ImageGenerated $event): void
    {
        TokenUsage::create([
            'invocation_id' => $event->invocationId,
            'type' => 'image',
            'provider' => $event->provider->name(),  
            'model' => $event->model,
            'input_tokens' => $event->response->usage->promptTokens,
            'output_tokens' => $event->response->usage->completionTokens,
            'cache_write_input_tokens' => $event->response->usage->cacheWriteInputTokens,
            'cache_read_input_tokens' => $event->response->usage->cacheReadInputTokens,
            'reasoning_tokens' => $event->response->usage->reasoningTokens,
        ]);
    }
}
