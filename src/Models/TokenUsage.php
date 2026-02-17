<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Models;

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
        'cache_write_input_tokens',
        'cache_read_input_tokens',
        'reasoning_tokens',
        'total_tokens',
        'input_cost',
        'output_cost',
        'cache_write_input_cost',
        'cache_read_input_cost',
        'reasoning_cost',
        'total_cost',
    ];
}
