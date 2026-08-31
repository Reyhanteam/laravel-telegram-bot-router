<?php

namespace ReyhanTeam\TelegramBotRouter\Core;

use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramExceptionHandler;
use ReyhanTeam\TelegramBotRouter\Providers\PollingProvider;
use ReyhanTeam\TelegramBotRouter\Providers\WebhookProvider;
use Throwable;

class UpdateManager
{
    public function handleWebhook()
    {
        $config = config('telegram-bot-router');

        if (($config['mode'] ?? 'webhook') !== 'webhook') {
            return response()->json([
                'error' => 'Webhook is disabled (current mode: '.($config['mode'] ?? 'unknown').')',
            ], 403);
        }

        $router = app('telegram.router');

        try {
            $provider = new WebhookProvider($router, $config);
            $provider->start();
        } catch (Throwable $e) {
            $handlerClass = $config['exceptions']['handler'] ?? TelegramExceptionHandler::class;
            app()->make($handlerClass)->handle($e, ['source' => 'webhook']);

            return response()->json(['error' => 'Telegram update could not be processed.'], 400);
        }

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
