<?php

declare(strict_types=1);

use HarlewDev\AiUsage\Enums\Token;
use HarlewDev\AiUsage\Http\Middleware\Authorize;

return [
    'enabled' => env('AI_USAGE_ENABLED', true),

    'dashboard' => [
        'enabled' => env('AI_USAGE_DASHBOARD_ENABLED', true),

        'route' => [
            'middleware' => [
                'web',
                Authorize::class,
            ],
            'path' => 'ai/usage',
            'name' => 'ai.usage',
        ],
    ],

    'listeners' => [
        'agent' => true,
        'image' => true,
        'embeddings' => true,
        'audio' => true,
    ],
];
