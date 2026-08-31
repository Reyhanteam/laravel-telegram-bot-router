<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

interface TelegramMiddlewareInterface
{
    public function handle(
        TelegramUpdate $update,
        Closure $next,
        ...$parameters
    ): mixed;
}
