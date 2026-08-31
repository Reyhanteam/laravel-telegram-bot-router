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
        $message = $this->sanitizeString($exception->getMessage());

        if ($exception instanceof TelegramRouteException) {
            $safeContext['route'] = $this->sanitizeString($exception->routePattern);
        }

        if ($exception instanceof TelegramApiException) {
            $safeContext['status_code'] = $exception->statusCode;
            $safeContext['method'] = $exception->method;
            $safeContext['telegram_error_code'] = $exception->errorCode;
        }

        Log::log($logLevel, 'Telegram router exception', [
            'exception' => get_class($exception),
            'message' => $message,
            ...$safeContext,
        ]);
    }

    private function sanitize(array $context): array
    {
        array_walk_recursive($context, function (&$value, $key) {
            if (is_string($key) && preg_match('/token|secret|password|authorization|api[_-]?key/i', $key)) {
                $value = '[REDACTED]';
                return;
            }

            if (is_string($value)) {
                $value = $this->sanitizeString($value);
            }
        });

        return $context;
    }

    private function sanitizeString(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $token = (string) config('telegram-bot-router.token', '');

        if ($token !== '') {
            $value = str_replace($token, '[REDACTED]', $value);
        }

        return preg_replace('/\b\d{8,12}:[A-Za-z0-9_-]{20,}\b/', '[REDACTED_TELEGRAM_TOKEN]', $value) ?? $value;
    }
}
