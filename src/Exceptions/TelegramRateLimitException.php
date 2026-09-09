<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use RuntimeException;

final class TelegramRateLimitException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $retryAfter,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 429, $previous);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
