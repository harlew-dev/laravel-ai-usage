<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Observers;

use Harlew\Ai\Usage\Enums\Token;
use Harlew\Ai\Usage\Facades\AiUsage;
use Harlew\Ai\Usage\Models\TokenUsage;
use Illuminate\Support\Collection;

class TokenUsageObserver
{
    public function creating(TokenUsage $tokenUsage): void
    {
        $tokenUsage->total_tokens = $tokenUsage->calculatedTotalTokens();
    }

    public function updating(TokenUsage $tokenUsage): void
    {
        $tokenUsage->total_tokens = $tokenUsage->calculatedTotalTokens();
    }
}