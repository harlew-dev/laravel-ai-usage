<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Listeners;

use HarlewDev\AiUsage\Models\TokenUsage;
use Laravel\Ai\Events\EmbeddingsGenerated;

class EmbeddingsUsage
{
    /**
     * Handle the event.
     */
    public function handle(EmbeddingsGenerated $event): void
    {
        TokenUsage::create([
            'invocation_id' => $event->invocationId,
            'type' => 'embedding',
            'provider' => $event->provider->name(),
            'model' => $event->model,
            'input_tokens' => $event->response->tokens,
        ]);
    }
}
