<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use ReyhanTeam\TelegramBotRouter\TelegramBot;

class MiddlewareGroupRegistrar
{
    public function __construct(protected array $middleware)
    {
    }

    public function onCommand(string $command, $callback): void
    {
        TelegramBot::addMiddlewareRoute('command', $command, $callback, $this->middleware);
    }

    public function onText(string $pattern, $callback): void
    {
        TelegramBot::addMiddlewareRoute('text', $pattern, $callback, $this->middleware);
    }

    public function onCallbackQuery($callback): void
    {
        TelegramBot::addMiddlewareRoute('callback_query', null, $callback, $this->middleware);
    }
}
