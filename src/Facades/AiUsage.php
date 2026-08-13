<?php

declare(strict_types=1);

namespace Harlew\Ai\Usage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|\Harlew\Ai\Usage\AiUsage css(string|\Illuminate\Contracts\Support\Htmlable|array|null $css = null)
 * @method static string|\Harlew\Ai\Usage\AiUsage js(string|\Illuminate\Contracts\Support\Htmlable|array|null $js = null)
 *
 * @see \Harlew\Ai\Usage\AiUsage
 */
class AiUsage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Harlew\Ai\Usage\AiUsage::class;
    }
}
