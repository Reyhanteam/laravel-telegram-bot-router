<?php

return [

    'token' => env('TELEGRAM_BOT_TOKEN', ''),
    'mode' => env('TELEGRAM_BOT_MODE', 'webhook'),

    'webhook' => [
        'path' => env('TELEGRAM_WEBHOOK_PATH', '/telegram/webhook'),
        'url' => env('TELEGRAM_WEBHOOK_URL', ''),
    ],

    'polling' => [
        'interval' => (int) env('TELEGRAM_POLLING_INTERVAL', 1500),
        'timeout' => (int) env('TELEGRAM_POLLING_TIMEOUT', 30),
        'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
    ],

    'queue' => [
        'updates' => (bool) env('TELEGRAM_QUEUE_UPDATES', false),
        'queue' => env('TELEGRAM_QUEUE_NAME', 'default'),
    ],

    'middleware' => [
        'aliases' => [
            // 'admin' => App\Telegram\Middleware\IsAdmin::class,
        ],
    ],

    'conversation' => [
        'ttl' => (int) env('TELEGRAM_CONVERSATION_TTL', 3600),
        'cache_store' => env('TELEGRAM_CONVERSATION_CACHE_STORE', null),
    ],

    'rate_limit' => [
        'enabled' => (bool) env('TELEGRAM_RATE_LIMIT_ENABLED', false),
        'prefix' => env('TELEGRAM_RATE_LIMIT_PREFIX', 'telegram_bot_router.rate_limit'),
        'limits' => [
            'user' => ['max_attempts' => (int) env('TELEGRAM_RATE_LIMIT_USER_MAX', 60), 'decay_seconds' => (int) env('TELEGRAM_RATE_LIMIT_USER_DECAY', 60)],
            'chat' => ['max_attempts' => (int) env('TELEGRAM_RATE_LIMIT_CHAT_MAX', 120), 'decay_seconds' => (int) env('TELEGRAM_RATE_LIMIT_CHAT_DECAY', 60)],
            'command' => ['max_attempts' => (int) env('TELEGRAM_RATE_LIMIT_COMMAND_MAX', 30), 'decay_seconds' => (int) env('TELEGRAM_RATE_LIMIT_COMMAND_DECAY', 60)],
        ],
    ],

    'exceptions' => [
        'handler' => ReyhanTeam\TelegramBotRouter\Exceptions\TelegramExceptionHandler::class,
        'log_level' => env('TELEGRAM_EXCEPTION_LOG_LEVEL', 'error'),
    ],

];
