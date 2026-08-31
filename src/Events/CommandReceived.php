<?php

namespace ReyhanTeam\TelegramBotRouter\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class CommandReceived
{
    public function __construct(public readonly TelegramUpdate $update)
    {
    }
}
