<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use ReyhanTeam\TelegramBotRouter\TelegramBot;
use ReyhanTeam\TelegramBotRouter\TelegramRouteRegistrar;

class TelegramMiddlewareRegistrar
{
    public function __construct(protected array $middleware)
    {
    }

    public function onCommand(string $command, $callback): TelegramRouteRegistrar
    {
        return TelegramBot::addMiddlewareRoute('command', $command, $callback, $this->middleware);
    }

    public function onText(string $pattern, $callback): TelegramRouteRegistrar
    {
        return TelegramBot::addMiddlewareRoute('text', $pattern, $callback, $this->middleware);
    }

    public function onCallbackQuery($callback): TelegramRouteRegistrar
    {
        return TelegramBot::addMiddlewareRoute('callback_query', null, $callback, $this->middleware);
    }
}
