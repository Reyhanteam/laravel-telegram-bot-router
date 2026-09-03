<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

final class TelegramApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }
}
