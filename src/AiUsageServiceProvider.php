<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage;

use HarlewDev\AiUsage\Listeners\AgentUsage;
use HarlewDev\AiUsage\Listeners\AudioUsage;
use HarlewDev\AiUsage\Listeners\EmbeddingsUsage;
use HarlewDev\AiUsage\Listeners\ImageUsage;
use HarlewDev\AiUsage\Livewire\Dashboard;
use HarlewDev\AiUsage\Livewire\Usage;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\AgentStreamed;
use Laravel\Ai\Events\AudioGenerated;
use Laravel\Ai\Events\EmbeddingsGenerated;
use Laravel\Ai\Events\ImageGenerated;
use Livewire\LivewireManager;

class AiUsageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ai/usage.php', 'ai.usage');
        $this->mergeConfigFrom(__DIR__.'/../config/ai/pricing.php', 'ai.pricing');

        $this->app->singleton(AiUsage::class);
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->loadMigrations();

        if (! config('ai.usage.enabled', true)) {
            return;
        }

        $this->registerEventListeners();
        $this->registerDashboard();
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/ai/usage.php' => config_path('ai/usage.php'),
            __DIR__.'/../config/ai/pricing.php' => config_path('ai/pricing.php'),
        ], 'ai-usage');
    }

    protected function registerEventListeners(): void
    {
        $this->app->booted(function () {
            $this->callAfterResolving(Dispatcher::class, function (Dispatcher $event, Application $app) {
                $listeners = config('ai.usage.listeners', []);

                if ($listeners['agent'] ?? true) {
                    $event->listen(AgentPrompted::class, AgentUsage::class);
                    $event->listen(AgentStreamed::class, AgentUsage::class);
                }

                if ($listeners['image'] ?? true) {
                    $event->listen(ImageGenerated::class, ImageUsage::class);
                }

                if ($listeners['embeddings'] ?? true) {
                    $event->listen(EmbeddingsGenerated::class, EmbeddingsUsage::class);
                }

                if ($listeners['audio'] ?? true) {
                    $event->listen(AudioGenerated::class, AudioUsage::class);
                }
            });
        });
    }

    protected function registerDashboard(): void
    {
        if (! config('ai.usage.dashboard.enabled', true)) {
            return;
        }

        $this->registerAuthorization();
        $this->registerResources();
        $this->registerComponents();
        $this->registerRoutes();
    }

    protected function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ai-usage');
    }

    protected function registerComponents(): void
    {
        $this->callAfterResolving('blade.compiler', function (BladeCompiler $blade) {
            $blade->anonymousComponentPath(__DIR__.'/../resources/views/components', 'ai-usage');
        });

        $this->callAfterResolving(LivewireManager::class, function (LivewireManager $livewire) {
            $livewire->addNamespace('ai-usage', classNamespace: 'HarlewDev\\AiUsage\\Livewire');
            $livewire->component('ai-usage::dashboard', Dashboard::class);
            $livewire->component('ai-usage::usage', Usage::class);
        });
    }

    protected function registerAuthorization(): void
    {
        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
            $gate->define('viewAiUsage', fn ($user = null) => $app->environment('local'));
        });
    }

    protected function registerRoutes(): void
    {
        $this->callAfterResolving('router', function (Router $router, Application $app) {
            $router->group([
                'domain' => $app->make('config')->get('ai.usage.dashboard.route.domain', null),
                'prefix' => $app->make('config')->get('ai.usage.dashboard.route.path', 'ai-usage'),
                'middleware' => $app->make('config')->get('ai.usage.dashboard.route.middleware'),
            ], function (Router $router) use ($app) {
                $router->get('/', Dashboard::class)
                    ->name($app->make('config')->get('ai.usage.dashboard.route.name', 'ai.usage'));
            });
        });
    }
}
