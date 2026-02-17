<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use RuntimeException;

class AiUsage
{
    /**
     * The CSS paths to include on the dashboard.
     *
     * @var list<string|Htmlable>
     */
    protected array $css = [__DIR__.'/../dist/dashboard.css'];

    /**
     * The JS paths to include on the dashboard.
     *
     * @var list<string|Htmlable>
     */
    protected array $js = [__DIR__.'/../dist/dashboard.js'];

    /**
     * Register or return CSS for the AI Usage dashboard.
     */
    public function css(string|Htmlable|array|null $css = null): string|self
    {
        if (func_num_args() === 1) {
            $this->css = array_values(array_unique(array_merge($this->css, Arr::wrap($css)), SORT_REGULAR));

            return $this;
        }

        return collect($this->css)->reduce(function ($carry, $css) {
            if (($contents = @file_get_contents($css)) === false) {
                throw new RuntimeException("Unable to load AI Usage dashboard CSS path [$css].");
            }

            return $carry."<style>{$contents}</style>".PHP_EOL;
        });
    }

    /**
     * Register or return JS for the AI Usage dashboard.
     */
    public function js(string|Htmlable|array|null $js = null): string|self
    {
        if (func_num_args() === 1) {
            $this->js = array_values(array_unique(array_merge($this->js, Arr::wrap($js)), SORT_REGULAR));

            return $this;
        }

        return collect($this->js)->reduce(function ($carry, $js) {
            if (($contents = @file_get_contents($js)) === false) {
                throw new RuntimeException("Unable to load AI Usage dashboard JS path [$js].");
            }

            return $carry."<script>{$contents}</script>".PHP_EOL;
        });
    }
}
