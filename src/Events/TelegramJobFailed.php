<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Events;

use Throwable;

/**
 * Dispatched after Laravel exhausts all attempts for a Telegram queue job.
 */
final class TelegramJobFailed
{
    public function __construct(
        public readonly string $job,
        public readonly array $context,
        public readonly Throwable $exception,
        public readonly int $attempts,
    ) {
    }
}
