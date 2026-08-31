<?php

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use RuntimeException;
use Throwable;

class TelegramRouteException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?string $routePattern = null,
        public readonly mixed $routeAction = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
