<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Tests;

use HarlewDev\AiUsage\AiUsageServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            AiUsageServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');

        $app['config']->set('ai.usage.enabled', true);
        $app['config']->set('ai.usage.dashboard.enabled', true);
        $app['config']->set('ai.providers', [
            'openai' => ['driver' => 'openai'],
            'anthropic' => ['driver' => 'anthropic'],
        ]);
    }
}
