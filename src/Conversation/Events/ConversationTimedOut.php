<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationTimedOut
{
    public function __construct(
        public TelegramUpdate $update,
        public string $name,
        public array $data = [],
    ) {
    }
}
