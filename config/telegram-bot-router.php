<?php

return [
    'telegram' => [
        'token' => env('TELEGRAM_BOT_TOKEN', ''),
    ],

    'webhook' => [
        'path' => env('TELEGRAM_WEBHOOK_PATH', '/telegram/webhook'),
        'url' => env('TELEGRAM_WEBHOOK_URL', ''),
    ],

    'polling' => [
        'timeout' => env('TELEGRAM_POLLING_TIMEOUT', 30),
        'limit' => env('TELEGRAM_POLLING_LIMIT', 100),
    ],

    'middleware' => [
        'aliases' => [
            // 'admin' => App\Telegram\Middleware\IsAdmin::class,
            // 'auth' => App\Telegram\Middleware\CheckUser::class,
        ],
    ],

    'conversation' => [
        'enabled' => true,
        'ttl' => env('TELEGRAM_CONVERSATION_TTL', 3600),
        'cache_store' => env('TELEGRAM_CONVERSATION_CACHE_STORE', null),
    ],
];
