<?php

declare(strict_types=1);

return [

    'token' => env('TELEGRAM_BOT_TOKEN', ''),
    'mode' => env('TELEGRAM_BOT_MODE', 'webhook'),

    'webhook' => [
        'path' => env('TELEGRAM_WEBHOOK_PATH', '/telegram/webhook'),
        'url' => env('TELEGRAM_WEBHOOK_URL', ''),
        'secret_token' => env('TELEGRAM_WEBHOOK_SECRET_TOKEN', ''),
    ],

    'polling' => [
        'interval' => (int) env('TELEGRAM_POLLING_INTERVAL', 1500),
        'timeout' => (int) env('TELEGRAM_POLLING_TIMEOUT', 30),
        'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
        'allowed_updates' => [
            'message',
            'edited_message',
            'channel_post',
            'edited_channel_post',
            'inline_query',
            'callback_query',
            'chat_member',
            'my_chat_member',
            'chat_join_request',
        ],
    ],

    'queue' => [
        'updates' => (bool) env('TELEGRAM_QUEUE_UPDATES', false),
        'connection' => env('TELEGRAM_QUEUE_CONNECTION', null),
        'queue' => env('TELEGRAM_QUEUE_NAME', 'default'),
        'tries' => (int) env('TELEGRAM_QUEUE_TRIES', 3),
        'backoff' => array_values(array_filter(array_map('trim', explode(',', env('TELEGRAM_QUEUE_BACKOFF', '10,30,60'))), 'strlen')),
        'timeout' => (int) env('TELEGRAM_QUEUE_TIMEOUT', 120),
        'middleware' => [],
        'deduplicate_updates' => (bool) env('TELEGRAM_QUEUE_DEDUPLICATE_UPDATES', true),
        'deduplication_ttl' => (int) env('TELEGRAM_QUEUE_DEDUPLICATION_TTL', 86400),
        'cache_store' => env('TELEGRAM_QUEUE_CACHE_STORE', null),

        // Non-retryable exceptions take precedence over retryable exceptions.
        // An empty retryable list means all exceptions are retryable unless
        // they are explicitly listed as non-retryable.
        'retryable_exceptions' => [],
        'non_retryable_exceptions' => [
            ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException::class,
            ReyhanTeam\TelegramBotRouter\Exceptions\TelegramRouteException::class,
        ],
    ],

    'route_cache' => [
        'key' => env('TELEGRAM_ROUTE_CACHE_KEY', 'telegram_bot_router.routes'),
    ],

    'middleware' => [
        'aliases' => [
            // 'admin' => App\Telegram\Middleware\IsAdmin::class,
        ],
    ],

    'authorization' => [
        'admin_user_ids' => array_values(array_filter(array_map('trim', explode(',', env('TELEGRAM_ADMIN_USER_IDS', ''))))),
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

    'outgoing_rate_limit' => [
        // Protect every outgoing Telegram Bot API request. Disabled by default
        // to preserve existing package behavior until the application enables it.
        'enabled' => (bool) env('TELEGRAM_OUTGOING_RATE_LIMIT_ENABLED', false),
        'prefix' => env('TELEGRAM_OUTGOING_RATE_LIMIT_PREFIX', 'telegram_bot_router.outgoing_rate_limit'),
        'max_attempts' => (int) env('TELEGRAM_OUTGOING_RATE_LIMIT_MAX', 30),
        'decay_seconds' => (int) env('TELEGRAM_OUTGOING_RATE_LIMIT_DECAY', 1),

        // Queue workers do not sleep while waiting for the outgoing limit.
        // They receive a retryable exception and use Laravel queue backoff.
        'queue_aware' => (bool) env('TELEGRAM_OUTGOING_RATE_LIMIT_QUEUE_AWARE', true),

        // Telegram 429 handling. retry_after takes precedence over the local
        // backoff values. Local backoff is used when Telegram gives no delay.
        'retry_after' => [
            'enabled' => (bool) env('TELEGRAM_RETRY_AFTER_ENABLED', true),
            'max_retries' => (int) env('TELEGRAM_RETRY_AFTER_MAX_RETRIES', 3),
            'backoff' => array_values(array_filter(array_map('trim', explode(',', env('TELEGRAM_RETRY_AFTER_BACKOFF', '1,2,4'))), 'strlen')),
        ],
    ],

    'exceptions' => [
        'handler' => ReyhanTeam\TelegramBotRouter\Exceptions\TelegramExceptionHandler::class,
        'log_level' => env('TELEGRAM_EXCEPTION_LOG_LEVEL', 'error'),
    ],

];
