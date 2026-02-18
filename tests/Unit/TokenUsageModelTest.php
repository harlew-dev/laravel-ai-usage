<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Models\TokenUsage;
use HarlewDev\AiUsage\Tests\TestCase;
use Illuminate\Support\Facades\Schema;

uses(TestCase::class);

it('creates the token_usages table from package migrations', function (): void {
    expect(Schema::hasTable('token_usages'))->toBeTrue();
});

it('calculates total tokens when creating a usage record', function (): void {
    $usage = fakeTokenUsage([
        'input_tokens' => 20,
        'output_tokens' => 10,
        'cache_write_tokens' => 3,
        'cache_read_tokens' => 2,
        'reasoning_tokens' => 1,
    ]);

    expect($usage->total_tokens)->toBe(36);
});

it('recalculates total tokens when updating a usage record', function (): void {
    $usage = fakeTokenUsage([
        'input_tokens' => 10,
        'output_tokens' => 10,
        'cache_write_tokens' => 0,
        'cache_read_tokens' => 0,
        'reasoning_tokens' => 0,
    ]);

    $usage->update([
        'input_tokens' => 30,
        'output_tokens' => 20,
        'cache_write_tokens' => 5,
        'cache_read_tokens' => 4,
        'reasoning_tokens' => 1,
    ]);

    expect($usage->fresh()->total_tokens)->toBe(60);
});

it('exposes a deterministic calculated total token count', function (): void {
    $usage = new TokenUsage([
        'input_tokens' => 7,
        'output_tokens' => 5,
        'cache_write_tokens' => 4,
        'cache_read_tokens' => 3,
        'reasoning_tokens' => 2,
    ]);

    expect($usage->calculatedTotalTokens())->toBe(21);
});
