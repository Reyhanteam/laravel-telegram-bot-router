<?php

declare(strict_types=1);

namespace Tests\Fixtures;

final class TelegramTestController
{
    public static bool $executed = false;

    public function start(): void
    {
        self::$executed = true;
    }
}
