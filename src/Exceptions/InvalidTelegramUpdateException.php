<?php

namespace ReyhanTeam\TelegramBotRouter\Exceptions;

use RuntimeException;

class InvalidTelegramUpdateException extends RuntimeException
{
    public function __construct(string $message = 'Invalid Telegram update received.')
    {
        parent::__construct($message);
    }
}
