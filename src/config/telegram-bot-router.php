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

    'conversation' => [
        'ttl' => (int) env('TELEGRAM_CONVERSATION_TTL', 3600),
    ],

];
