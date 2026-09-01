<?php

namespace ReyhanTeam\TelegramBotRouter;

use ReyhanTeam\TelegramBotRouter\Jobs\SendTelegramMessageJob;

class TelegramQueue
{
    public static function sendMessage(array $params): SendTelegramMessageJob
    {
        return SendTelegramMessageJob::dispatch($params);
    }
}
