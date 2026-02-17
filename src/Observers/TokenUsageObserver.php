<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Observers;

use HarlewDev\AiUsage\Enums\Token;
use HarlewDev\AiUsage\Facades\AiUsage;
use HarlewDev\AiUsage\Models\TokenUsage;
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