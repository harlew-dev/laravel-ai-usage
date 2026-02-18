<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Tests\Support;

use HarlewDev\AiUsage\Tests\TestCase;

abstract class ListenersDisabledTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ai.usage.listeners', [
            'agent' => false,
            'image' => false,
            'embeddings' => false,
            'audio' => false,
        ]);
    }
}
