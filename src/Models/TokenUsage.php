<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Models;

use HarlewDev\AiUsage\Enums\Token;
use HarlewDev\AiUsage\Observers\TokenUsageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy(TokenUsageObserver::class)]
class TokenUsage extends Model
{
    use HasFactory;

    protected $table = 'token_usages';

    protected $fillable = [
        'invocation_id',
        'type',
        'agent',
        'provider',
        'model',
        'input_tokens',
        'output_tokens',
        'cache_write_tokens',
        'cache_read_tokens',
        'reasoning_tokens',
        'total_tokens',
    ];

    public function calculatedTotalTokens(): int
    {
        return $this->input_tokens 
            + $this->output_tokens 
            + $this->cache_write_tokens 
            + $this->cache_read_tokens 
            + $this->reasoning_tokens;
    }
}
