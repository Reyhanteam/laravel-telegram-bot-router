<?php

namespace ReyhanTeam\TelegramBotRouter\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class CallbackQueryReceived
{
    public function __construct(public readonly TelegramUpdate $update)
    {
    }
}
