# Queue Reliability

Laravel Queue is optional. Enable queued update processing with `TELEGRAM_QUEUE_UPDATES=true` and configure the queue connection and worker in the Laravel application.

## Retry behavior

Telegram queue jobs use Laravel's normal retry lifecycle. Configure:

```env
TELEGRAM_QUEUE_TRIES=3
TELEGRAM_QUEUE_BACKOFF=10,30,60
TELEGRAM_QUEUE_TIMEOUT=120
TELEGRAM_QUEUE_NAME=telegram-high
TELEGRAM_QUEUE_CONNECTION=database
```

`tries` controls the maximum attempts. `backoff` controls the delay before later attempts. `timeout` controls the maximum execution time for the job.

## Retryable and non-retryable exceptions

The package now has an explicit exception policy in `TelegramQueueExceptionPolicy`.

- `non_retryable_exceptions` always wins.
- If `retryable_exceptions` is empty, exceptions not listed as non-retryable are retryable.
- If `retryable_exceptions` is not empty, only matching exceptions are retryable.

The default policy treats malformed Telegram updates and invalid Telegram routes as non-retryable. This prevents deterministic bad input or route configuration from consuming all retry attempts.

Example:

```php
'queue' => [
    'retryable_exceptions' => [
        \ReyhanTeam\TelegramBotRouter\Exceptions\TelegramApiException::class,
    ],
    'non_retryable_exceptions' => [
        \ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException::class,
        \ReyhanTeam\TelegramBotRouter\Exceptions\TelegramRouteException::class,
    ],
],
```

For an application-specific policy, add the exception classes that represent temporary failures to `retryable_exceptions` and deterministic failures to `non_retryable_exceptions`.

## Failed jobs and inspection

The package does **not** create a second failed-update table. Laravel's `failed_jobs` storage is the canonical persistence layer for failed queue jobs. The package adds `TelegramJobFailed` and failure logs with safe inspection context such as the job class, update ID, route or Telegram method, attempt count and exception.

This avoids storing the full Telegram payload twice. If the application needs the full payload for auditing, it should use its own application-level storage and data-retention policy.

Inspect failed jobs with Laravel:

```bash
php artisan queue:failed
php artisan queue:retry all
php artisan queue:forget <id>
```

A `TelegramJobFailed` listener can send alerts or record metrics:

```php
Event::listen(\ReyhanTeam\TelegramBotRouter\Events\TelegramJobFailed::class, function ($event) {
    // Alert, metric, or application-specific inspection.
});
```

## Real Laravel worker verification

Run a real Laravel worker in the application that uses the package:

```bash
php artisan queue:work --queue=telegram-high --tries=3 --timeout=120
```

Send a Telegram update that matches a queued route. Confirm that:

1. The update creates a queue job.
2. The worker receives the job.
3. A temporary exception causes a later attempt.
4. Backoff delays are applied.
5. A successful attempt removes the job.
6. An exhausted job appears in `failed_jobs`.
7. `TelegramJobFailed` is dispatched after permanent failure.

For local testing, the database queue is useful because failed jobs can be inspected with normal Laravel commands. Redis is recommended when the production workload requires higher queue throughput.

## Production worker operation

A production deployment must keep queue workers running outside the web request lifecycle. Use a process manager such as Supervisor, systemd, or a platform-managed worker service.

Recommended operational rules:

- Keep the worker timeout lower than the queue connection's visibility/`retry_after` window.
- Set `--tries` consistently with the package job configuration.
- Monitor `failed_jobs` and queue depth.
- Restart workers after package/application deployments with `php artisan queue:restart`.
- Use a dedicated Telegram queue when Telegram traffic must be isolated from unrelated application jobs.
- Do not put bot tokens or webhook secrets in logs.
- Review retryable exceptions before production use.

The package test suite covers retry configuration, retry behavior, exception-policy precedence, failed-job inspection context and queue dispatch. A real external worker cannot be proven by a package-only Testbench run, so the worker command above is the final application-level verification step.
