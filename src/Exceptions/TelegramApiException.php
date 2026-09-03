<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use RuntimeException;

final class TelegramApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $errorCode,
        private readonly array $parameters = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $errorCode, $previous);
    }

    public function getTelegramErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
