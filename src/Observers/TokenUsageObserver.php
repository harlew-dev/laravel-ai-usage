<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Observers;

use HarlewDev\AiUsage\Models\TokenUsage;
use Illuminate\Support\Collection;

class TokenUsageObserver
{
    public function creating(TokenUsage $tokenUsage): void
    {
        $this->setTokenCosts($tokenUsage);
        $this->setTotalCost($tokenUsage);
        $this->setTotalTokens($tokenUsage);
    }

    protected function setTotalCost(TokenUsage $tokenUsage): void
    {
        $totalCost = $this->getEnabledTokens()
            ->map(fn ($attribute) => $attribute . $this->getCostSuffix())
            ->sum(fn ($attribute) => $tokenUsage->getAttribute($attribute));

        $tokenUsage->setAttribute('total_cost', $totalCost);
    }

    protected function setTotalTokens(TokenUsage $tokenUsage): void
    {
        $totalTokens = $this->getEnabledTokens()
            ->map(fn ($attribute) => $attribute . $this->getTokenSuffix())
            ->sum(fn ($attribute) => $tokenUsage->getAttribute($attribute));

        $tokenUsage->setAttribute('total_tokens', $totalTokens);
    }

    protected function setTokenCosts(TokenUsage $tokenUsage): void
    {
        $pricing = $this->getPricing($tokenUsage->provider, $tokenUsage->model);

        if ($pricing->isEmpty()) {
            return;
        }

        $this->getEnabledTokens()
            ->filter(fn ($attribute) => $pricing->has($attribute))
            ->each(function ($attribute) use ($tokenUsage, $pricing) {
                $tokens = $tokenUsage->getAttribute($attribute . $this->getTokenSuffix());
                $price = $pricing->get($attribute);
                $multiplier = $pricing->get('token_cost_multiplier');

                $tokenUsage->setAttribute(
                    key: $attribute . $this->getCostSuffix(), 
                    value: $this->getTokenCost($price, $tokens, $multiplier)
                );
            });
    }

    protected function getTokenCost(?float $price, ?int $tokens, ?float $multiplier = null): ?float
    {
        if ($tokens == null || $tokens == 0 || $price === null) {
            return 0;
        }

        return $price * $tokens * ($multiplier ?? $this->getDefaultTokenCostMultiplier());
    }

    protected function getCostSuffix(): string
    {
        return config('ai.usage.model.cost_suffix', '_cost');
    }

    protected function getTokenSuffix(): string
    {
        return config('ai.usage.model.token_suffix', '_tokens');
    }

    protected function getDefaultTokenCostMultiplier(): float
    {
        return config('ai.usage.token_cost_multiplier', 0.000001);
    }
    
    protected function getPricing(string $provider, string $model): Collection
    {
        return collect(config("ai.pricing.{$provider}.{$model}") ?? []);
    }

    protected function getEnabledTokens(): Collection
    {
        return collect(config('ai.usage.tokens') ?? []);
    }
}