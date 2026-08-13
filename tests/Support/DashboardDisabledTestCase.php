<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Tests\Support;

use Harlew\Ai\Usage\Tests\TestCase;

abstract class DashboardDisabledTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ai.usage.dashboard.enabled', false);
    }
}
