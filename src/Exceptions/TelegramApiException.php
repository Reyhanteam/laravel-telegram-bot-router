<?php

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use RuntimeException;

class TelegramApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $method = null,
        public readonly ?int $errorCode = null,
    ) {
        parent::__construct($message);
    }
}
