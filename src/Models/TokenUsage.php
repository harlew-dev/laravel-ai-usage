<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Models;

use Harlew\Ai\Usage\Enums\Token;
use Harlew\Ai\Usage\Observers\TokenUsageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ObservedBy(TokenUsageObserver::class)]
class TokenUsage extends Model
{
    use HasFactory, HasUuids;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'invocation_id' => 'string',
            'type' => 'string',
            'agent' => 'string',
            'provider' => 'string',
            'model' => 'string',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'cache_write_tokens' => 'integer',
            'cache_read_tokens' => 'integer',
            'reasoning_tokens' => 'integer',
            'total_tokens' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function calculatedTotalTokens(): int
    {
        return $this->input_tokens 
            + $this->output_tokens 
            + $this->cache_write_tokens 
            + $this->cache_read_tokens 
            + $this->reasoning_tokens;
    }
}
