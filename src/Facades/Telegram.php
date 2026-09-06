<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Testing\TelegramFake;

final class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }

    public static function fake(): TelegramFake
    {
        $fake = new TelegramFake();
        static::swap($fake);
        return $fake;
    }
}
