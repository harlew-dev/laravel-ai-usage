<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Listeners;

use Harlew\Ai\Usage\Models\TokenUsage;
use Laravel\Ai\Events\AudioGenerated;

class AudioUsage
{
    public function handle(AudioGenerated $event): void
    {
        TokenUsage::create([
            'invocation_id' => $event->invocationId,
            'type' => 'audio',
            'provider' => $event->provider->name(),
            'model' => $event->model,
        ]);
    }
}