<?php

namespace ReyhanTeam\TelegramBotRouter\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class MessageReceived
{
    public function __construct(
        public readonly TelegramUpdate $update,
    ) {
    }

    public function message(): ?TelegramUpdate
    {
        return isset($this->update->message) ? $this->update->message : null;
    }
}
