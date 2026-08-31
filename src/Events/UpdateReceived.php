<?php

namespace ReyhanTeam\TelegramBotRouter\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class UpdateReceived
{
    public function __construct(
        public TelegramUpdate $update,
    ) {
    }
}
