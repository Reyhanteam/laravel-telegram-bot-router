<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\Events\TelegramJobFailed;
use Throwable;

abstract class TelegramQueueJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;
    public int|array $backoff;
    public int $timeout;

    protected ?string $deduplicationKey = null;

    protected function configureQueue(?string $queue = null): void
    {
        $config = config('telegram-bot-router.queue', []);

        $this->tries = max(1, (int) ($config['tries'] ?? 3));
        $this->timeout = max(1, (int) ($config['timeout'] ?? 120));
        $backoff = $config['backoff'] ?? [10, 30, 60];
        $this->backoff = is_array($backoff)
            ? array_values(array_map(static fn ($delay): int => max(0, (int) $delay), $backoff))
            : max(0, (int) $backoff);

        $connection = $config['connection'] ?? null;
        if (is_string($connection) && $connection !== '') {
            $this->onConnection($connection);
        }

        $this->onQueue($queue ?: (string) ($config['queue'] ?? 'default'));
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        $middleware = config('telegram-bot-router.queue.middleware', []);
        if (!is_array($middleware)) {
            return [];
        }

        return array_values(array_filter(array_map(static function ($item): ?object {
            if (is_object($item)) {
                return $item;
            }

            return is_string($item) && class_exists($item) ? app()->make($item) : null;
        }, $middleware)));
    }

    public function failed(Throwable $exception): void
    {
        $this->releaseDeduplication();

        $context = $this->queueContext();
        Log::error('Telegram queue job failed permanently.', [
            'job' => static::class,
            'attempts' => $this->attempts(),
            ...$context,
            'exception' => $exception,
        ]);

        event(new TelegramJobFailed(static::class, $context, $exception, $this->attempts()));
    }

    protected function run(callable $callback): mixed
    {
        if (!$this->claimDeduplication()) {
            Log::info('Skipped duplicate Telegram update queue job.', $this->queueContext());
            return null;
        }

        try {
            $result = $callback();
            Log::info('Telegram queue job processed.', [
                'job' => static::class,
                'attempts' => $this->attempts(),
                ...$this->queueContext(),
            ]);

            return $result;
        } catch (Throwable $exception) {
            // A retry of this same job must be allowed to claim the update again.
            $this->releaseDeduplication();
            Log::warning('Telegram queue job attempt failed.', [
                'job' => static::class,
                'attempts' => $this->attempts(),
                ...$this->queueContext(),
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }

    private function claimDeduplication(): bool
    {
        if ($this->deduplicationKey === null || !config('telegram-bot-router.queue.deduplicate_updates', true)) {
            return true;
        }

        return $this->cache()->add(
            $this->deduplicationKey,
            true,
            max(1, (int) config('telegram-bot-router.queue.deduplication_ttl', 86400)),
        );
    }

    private function releaseDeduplication(): void
    {
        if ($this->deduplicationKey !== null) {
            $this->cache()->forget($this->deduplicationKey);
        }
    }

    private function cache(): mixed
    {
        $store = config('telegram-bot-router.queue.cache_store');
        return is_string($store) && $store !== '' ? Cache::store($store) : Cache::store();
    }

    /** @return array<string, mixed> */
    abstract protected function queueContext(): array;
}
