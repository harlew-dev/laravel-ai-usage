<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Tests\Support;

use Harlew\Ai\Usage\Tests\TestCase;

abstract class LocalEnvironmentTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app->detectEnvironment(static fn (): string => 'local');
        $app['config']->set('app.env', 'local');
    }
}
