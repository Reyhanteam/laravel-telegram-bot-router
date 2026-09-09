<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Queue;

use Throwable;

final class TelegramQueueExceptionPolicy
{
    public function shouldRetry(Throwable $exception): bool
    {
        $config = config('telegram-bot-router.queue', []);

        $nonRetryable = $this->configuredClasses($config['non_retryable_exceptions'] ?? []);
        foreach ($nonRetryable as $class) {
            if ($exception instanceof $class) {
                return false;
            }
        }

        $retryable = $this->configuredClasses($config['retryable_exceptions'] ?? []);
        if ($retryable === []) {
            return true;
        }

        foreach ($retryable as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /** @return array<int, class-string> */
    private function configuredClasses(mixed $classes): array
    {
        if (!is_array($classes)) {
            return [];
        }

        return array_values(array_filter(
            $classes,
            static fn (mixed $class): bool => is_string($class) && class_exists($class),
        ));
    }
}
