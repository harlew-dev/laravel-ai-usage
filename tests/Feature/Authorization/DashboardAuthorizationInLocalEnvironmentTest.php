<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Tests\Support\LocalEnvironmentTestCase;

uses(LocalEnvironmentTestCase::class);

it('allows dashboard access in the local environment', function (): void {
    $this->get(route('ai.usage'))
        ->assertOk()
        ->assertSee('Usage');
});
