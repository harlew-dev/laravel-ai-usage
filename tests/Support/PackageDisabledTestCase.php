<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Tests\Support;

use Harlew\Ai\Usage\Tests\TestCase;

abstract class PackageDisabledTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ai.usage.enabled', false);
    }
}
