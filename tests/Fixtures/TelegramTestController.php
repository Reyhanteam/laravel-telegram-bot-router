<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use ReyhanTeam\TelegramBotRouter\Facades\Telegram;

final class TelegramTestController
{
    public static bool $executed = false;

    public function start(): void
    {
        self::$executed = true;
        Telegram::sendMessage(2001, 'Welcome!');
    }
}
