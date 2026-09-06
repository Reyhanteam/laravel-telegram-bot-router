<?php

namespace ReyhanTeam\TelegramBotRouter\Core;

use Illuminate\Http\Request;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramExceptionHandler;
use ReyhanTeam\TelegramBotRouter\Providers\PollingProvider;
use ReyhanTeam\TelegramBotRouter\Providers\WebhookProvider;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UpdateManager
{
    public function handleWebhook(Request $request)
    {
        $config = config('telegram-bot-router');

        if (($config['mode'] ?? 'webhook') !== 'webhook') {
            return response()->json([
                'error' => 'Webhook is disabled (current mode: '.($config['mode'] ?? 'unknown').')',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! $this->isWebhookRequestAuthorized($request, $config)) {
            return response()->json([
                'error' => 'Unauthorized webhook request.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $router = app('telegram.router');

        try {
            $provider = new WebhookProvider($router, $config);
            $provider->start($request);
        } catch (Throwable $e) {
            $handlerClass = $config['exceptions']['handler'] ?? TelegramExceptionHandler::class;
            app()->make($handlerClass)->handle($e, ['source' => 'webhook']);

            return response()->json(['error' => 'Telegram update could not be processed.'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['status' => 'ok']);
    }

    private function isWebhookRequestAuthorized(Request $request, array $config): bool
    {
        $configuredSecret = (string) data_get($config, 'webhook.secret_token', '');

        // An empty secret preserves backwards compatibility and means that
        // webhook authentication is explicitly disabled.
        if ($configuredSecret === '') {
            return true;
        }

        $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if ($providedSecret === '') {
            return false;
        }

        return hash_equals($configuredSecret, $providedSecret);
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
