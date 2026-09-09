<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Events\TelegramJobFailed;
use ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramRouteException;
use ReyhanTeam\TelegramBotRouter\Jobs\TelegramQueueJob;
use ReyhanTeam\TelegramBotRouter\Queue\TelegramQueueExceptionPolicy;
use ReyhanTeam\TelegramBotRouter\TelegramRouterServiceProvider;
use RuntimeException;
use Throwable;

final class QueueReliabilityTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TelegramRouterServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'telegram-bot-router.queue.tries' => 3,
            'telegram-bot-router.queue.backoff' => [1, 2, 3],
            'telegram-bot-router.queue.timeout' => 30,
            'telegram-bot-router.queue.deduplicate_updates' => false,
        ]);
    }

    public function test_retry_configuration_is_applied_to_telegram_queue_jobs(): void
    {
        $job = new TestTelegramQueueJob();

        $this->assertSame(3, $job->tries);
        $this->assertSame([1, 2, 3], $job->backoff);
        $this->assertSame(30, $job->timeout);
        $this->assertSame('default', $job->queue);
    }

    public function test_retryable_exception_is_rethrown_and_can_be_retried(): void
    {
        $job = new TestTelegramQueueJob();
        $job->failuresBeforeSuccess = 1;

        try {
            $job->handle();
            $this->fail('The first attempt must fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('temporary failure', $exception->getMessage());
        }

        $job->handle();

        $this->assertSame(2, $job->executions);
    }

    public function test_non_retryable_exception_is_failed_immediately(): void
    {
        Event::fake();
        Log::fake();
        config([
            'telegram-bot-router.queue.non_retryable_exceptions' => [InvalidTelegramUpdateException::class],
            'telegram-bot-router.queue.retryable_exceptions' => [],
        ]);

        $job = new TestTelegramQueueJob();
        $job->exception = new InvalidTelegramUpdateException('invalid update');
        $job->handle();

        $this->assertTrue($job->failedImmediately);
        $this->assertSame(1, $job->executions);
        Event::assertDispatched(TelegramJobFailed::class);
    }

    public function test_non_retryable_policy_takes_precedence_over_retryable_policy(): void
    {
        config([
            'telegram-bot-router.queue.retryable_exceptions' => [RuntimeException::class],
            'telegram-bot-router.queue.non_retryable_exceptions' => [RuntimeException::class],
        ]);

        $policy = new TelegramQueueExceptionPolicy();

        $this->assertFalse($policy->shouldRetry(new RuntimeException('permanent')));
    }

    public function test_default_policy_retries_unknown_transient_failures(): void
    {
        config([
            'telegram-bot-router.queue.retryable_exceptions' => [],
            'telegram-bot-router.queue.non_retryable_exceptions' => [
                InvalidTelegramUpdateException::class,
                TelegramRouteException::class,
            ],
        ]);

        $policy = new TelegramQueueExceptionPolicy();

        $this->assertTrue($policy->shouldRetry(new RuntimeException('temporary network failure')));
        $this->assertFalse($policy->shouldRetry(new InvalidTelegramUpdateException('invalid')));
        $this->assertFalse($policy->shouldRetry(new TelegramRouteException('invalid route')));
    }

    public function test_failed_job_event_contains_inspection_context(): void
    {
        Event::fake();
        Log::fake();

        $job = new TestTelegramQueueJob();
        $exception = new RuntimeException('permanent failure');
        $job->failed($exception);

        Event::assertDispatched(TelegramJobFailed::class, function (TelegramJobFailed $event) use ($exception): bool {
            return $event->job === TestTelegramQueueJob::class
                && $event->context['update_id'] === 12345
                && $event->context['route'] === 'test'
                && $event->exception === $exception;
        });
    }

    public function test_queue_dispatch_works_with_laravel_bus(): void
    {
        Bus::fake();

        dispatch(new TestTelegramQueueJob());

        Bus::assertDispatched(TestTelegramQueueJob::class);
    }
}

final class TestTelegramQueueJob extends TelegramQueueJob
{
    public int $executions = 0;
    public int $failuresBeforeSuccess = 0;
    public bool $failedImmediately = false;
    public ?Throwable $exception = null;

    public function __construct()
    {
        $this->configureQueue();
    }

    public function handle(): void
    {
        $this->run(function (): void {
            $this->executions++;

            if ($this->exception !== null) {
                $exception = $this->exception;
                $this->exception = null;
                throw $exception;
            }

            if ($this->failuresBeforeSuccess > 0) {
                $this->failuresBeforeSuccess--;
                throw new RuntimeException('temporary failure');
            }
        });
    }

    public function fail(Throwable $exception = null): void
    {
        $this->failedImmediately = true;
        parent::fail($exception);
    }

    protected function queueContext(): array
    {
        return [
            'update_id' => 12345,
            'route' => 'test',
        ];
    }
}
