<?php

namespace ReyhanTeam\TelegramBotRouter\Core;

use ReyhanTeam\TelegramBotRouter\Providers\PollingProvider;
use ReyhanTeam\TelegramBotRouter\Providers\WebhookProvider;

class UpdateManager
{
    //
    public function handleWebhook()
    {
        $config = config('telegram-bot-router');

        if (($config['mode'] ?? 'webhook') !== 'webhook') {
            return response()->json([
                'error' => 'Webhook is disabled (current mode: '.($config['mode'] ?? 'unknown').')',
            ], 403);
        }

        $router = app('telegram.router');
        $provider = new WebhookProvider($router, $config);
        $provider->start();

        return response()->json(['status' => 'ok']);
    }

    public function startPolling(): void
    {
        $config = config('telegram-bot-router');

        if (($config['mode'] ?? null) !== 'polling') {
            throw new \RuntimeException(
                'Polling mode is disabled. Current mode: '.($config['mode'] ?? 'unknown')
            );
        }

        $router = app('telegram.router');
        $provider = new PollingProvider($router, $config);
        $provider->start();
    }
}
