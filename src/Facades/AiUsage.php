<?php

declare(strict_types=1);

namespace HarlewDev\AiUsage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|\HarlewDev\AiUsage\AiUsage css(string|\Illuminate\Contracts\Support\Htmlable|array|null $css = null)
 * @method static string|\HarlewDev\AiUsage\AiUsage js(string|\Illuminate\Contracts\Support\Htmlable|array|null $js = null)
 *
 * @see \HarlewDev\AiUsage\AiUsage
 */
class AiUsage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HarlewDev\AiUsage\AiUsage::class;
    }
}
