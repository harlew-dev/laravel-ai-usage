<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Listeners;

use Harlew\Ai\Usage\Models\TokenUsage;
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
