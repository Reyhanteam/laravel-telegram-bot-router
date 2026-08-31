<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation\Events;

use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationStepCompleted
{
    public function __construct(
        public TelegramUpdate $update,
        public string $name,
        public int $step,
        public array $data = [],
    ) {
    }
}
