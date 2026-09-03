<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\TelegramBot;

final class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramBot::class;
    }
}
