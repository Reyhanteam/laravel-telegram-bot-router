<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\RateLimiting;

use Illuminate\Cache\RateLimiter;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramRateLimitException;

final class OutgoingTelegramRateLimiter
{
    public function __construct(
        private readonly RateLimiter $rateLimiter,
        private readonly string $token,
    ) {
    }

    /**
     * Acquire one outgoing Telegram API slot.
     *
     * HTTP requests wait when called from a normal request. Queue workers
     * throw a retryable exception instead, so Laravel can apply queue backoff
     * without blocking a worker process.
     */
    public function acquire(string $scope = 'api'): void
    {
        $config = $this->config();

        if (!($config['enabled'] ?? false)) {
            return;
        }

        $maxAttempts = max(1, (int) ($config['max_attempts'] ?? 30));
        $decaySeconds = max(1, (int) ($config['decay_seconds'] ?? 1));
        $key = $this->key($scope);

        while (true) {
            if (!$this->rateLimiter->tooManyAttempts($key, $maxAttempts)) {
                $this->rateLimiter->hit($key, $decaySeconds);
                return;
            }

            $delay = max(1, (int) $this->rateLimiter->availableIn($key));

            if ($this->isQueueWorker() && ($config['queue_aware'] ?? true)) {
                throw new TelegramRateLimitException(
                    sprintf('Telegram outgoing rate limit reached. Retry after %d second(s).', $delay),
                    $delay,
                );
            }

            sleep($delay);
        }
    }

    public function isQueueWorker(): bool
    {
        return function_exists('app')
            && app()->bound('queue.worker');
    }

    /** @return array<string, mixed> */
    private function config(): array
    {
        $config = config('telegram-bot-router.outgoing_rate_limit', []);

        return is_array($config) ? $config : [];
    }

    private function key(string $scope): string
    {
        $prefix = (string) ($this->config()['prefix'] ?? 'telegram_bot_router.outgoing_rate_limit');
        $bot = hash('sha256', $this->token);

        return sprintf('%s:%s:%s', trim($prefix, ':'), $bot, $scope);
    }
}
