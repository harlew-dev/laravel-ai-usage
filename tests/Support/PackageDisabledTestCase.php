<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Tests\Support;

use HarlewDev\AiUsage\Tests\TestCase;

abstract class PackageDisabledTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ai.usage.enabled', false);
    }
}
