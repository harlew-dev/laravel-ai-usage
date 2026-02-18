<?php

declare(strict_types=1);

use HarlewDev\AiUsage\AiUsage;
use HarlewDev\AiUsage\Tests\TestCase;

uses(TestCase::class);

it('renders the default dashboard css and js assets inline', function (): void {
    $aiUsage = app(AiUsage::class);

    expect($aiUsage->css())->toContain('<style>')
        ->and($aiUsage->css())->toContain('</style>')
        ->and($aiUsage->js())->toContain('<script>')
        ->and($aiUsage->js())->toContain('</script>');
});

it('registers additional css and js assets once even when duplicated', function (): void {
    $cssPath = tempnam(sys_get_temp_dir(), 'ai-usage-css');
    $jsPath = tempnam(sys_get_temp_dir(), 'ai-usage-js');

    file_put_contents($cssPath, 'body{color:red;}');
    file_put_contents($jsPath, 'window.__aiUsageTest=true;');

    $aiUsage = app(AiUsage::class);
    $aiUsage->css([$cssPath, $cssPath]);
    $aiUsage->js([$jsPath, $jsPath]);

    $css = $aiUsage->css();
    $js = $aiUsage->js();

    unlink($cssPath);
    unlink($jsPath);

    expect(substr_count($css, 'body{color:red;}'))->toBe(1)
        ->and(substr_count($js, 'window.__aiUsageTest=true;'))->toBe(1);
});

it('throws an exception when a configured css path cannot be read', function (): void {
    $aiUsage = app(AiUsage::class);
    $aiUsage->css('C:/definitely-missing/ai-usage.css');

    expect(fn (): string => $aiUsage->css())
        ->toThrow(RuntimeException::class, 'Unable to load AI Usage dashboard CSS path');
});

it('throws an exception when a configured js path cannot be read', function (): void {
    $aiUsage = app(AiUsage::class);
    $aiUsage->js('C:/definitely-missing/ai-usage.js');

    expect(fn (): string => $aiUsage->js())
        ->toThrow(RuntimeException::class, 'Unable to load AI Usage dashboard JS path');
});
