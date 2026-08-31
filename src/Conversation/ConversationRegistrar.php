<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use ReyhanTeam\TelegramBotRouter\TelegramBot;

class ConversationRegistrar
{
    protected array $steps = [];

    protected int $ttl = 3600;

    public function __construct(protected string $name)
    {
    }

    public function step($callback): static
    {
        $this->steps[] = $callback;
        return $this;
    }

    public function ttl(int $seconds): static
    {
        $this->ttl = $seconds;
        return $this;
    }

    public function register(): void
    {
        TelegramBot::addConversation($this->name, $this->steps, $this->ttl);
    }
}
