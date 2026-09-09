<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Response\TelegramResponse;
use ReyhanTeam\TelegramBotRouter\Testing\TelegramFake;

final class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }

    public static function response(int|string|null $chatId = null): TelegramResponse
    {
        return new TelegramResponse(app(TelegramApiClient::class), $chatId);
    }

    public static function fake(): TelegramFake
    {
        $fake = new TelegramFake();
        static::swap($fake);
        return $fake;
    }
}
