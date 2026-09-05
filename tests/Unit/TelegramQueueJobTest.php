<?php

declare(strict_types=1);

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Jobs\ProcessTelegramUpdateJob;
use ReyhanTeam\TelegramBotRouter\TelegramRouterServiceProvider;

final class TelegramQueueJobTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TelegramRouterServiceProvider::class];
    }

    public function test_queue_jobs_use_configured_retry_and_timeout_values(): void
    {
        config()->set('telegram-bot-router.queue.tries', 5);
        config()->set('telegram-bot-router.queue.backoff', [2, 5, 10]);
        config()->set('telegram-bot-router.queue.timeout', 90);
        config()->set('telegram-bot-router.queue.queue', 'telegram');
        config()->set('telegram-bot-router.queue.connection', 'sync');

        $job = new ProcessTelegramUpdateJob(['update_id' => 123], ['pattern' => 'start']);

        $this->assertSame(5, $job->tries);
        $this->assertSame([2, 5, 10], $job->backoff);
        $this->assertSame(90, $job->timeout);
        $this->assertSame('telegram', $job->queue);
        $this->assertSame('sync', $job->connection);
    }

    public function test_update_jobs_have_a_stable_deduplication_key(): void
    {
        $first = new ProcessTelegramUpdateJob(['update_id' => 123], ['pattern' => 'start']);
        $second = new ProcessTelegramUpdateJob(['update_id' => 123], ['pattern' => 'help']);

        $this->assertSame(
            $this->readDeduplicationKey($first),
            $this->readDeduplicationKey($second),
        );
    }

    private function readDeduplicationKey(ProcessTelegramUpdateJob $job): ?string
    {
        $property = new \ReflectionProperty($job, 'deduplicationKey');
        $property->setAccessible(true);

        return $property->getValue($job);
    }
}
