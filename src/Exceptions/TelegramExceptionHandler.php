<?php

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramExceptionHandler
{
    public function handle(Throwable $exception, array $context = []): void
    {
        $logLevel = config('telegram-bot-router.exceptions.log_level', 'error');
        $safeContext = $this->sanitize($context);

        if ($exception instanceof TelegramRouteException) {
            $safeContext['route'] = $exception->routePattern;
        }

        if ($exception instanceof TelegramApiException) {
            $safeContext['status_code'] = $exception->statusCode;
            $safeContext['method'] = $exception->method;
            $safeContext['telegram_error_code'] = $exception->errorCode;
        }

        Log::log($logLevel, 'Telegram router exception', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            ...$safeContext,
        ]);
    }

    private function sanitize(array $context): array
    {
        array_walk_recursive($context, function (&$value, $key) {
            if (is_string($key) && preg_match('/token|secret|password|authorization|api[_-]?key/i', $key)) {
                $value = '[REDACTED]';
            }
        });

        return $context;
    }
}
