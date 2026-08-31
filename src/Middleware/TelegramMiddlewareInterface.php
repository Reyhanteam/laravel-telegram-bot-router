<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

interface TelegramMiddlewareInterface
{
    public function handle(TelegramUpdate $update, \Closure $next): mixed;
}
