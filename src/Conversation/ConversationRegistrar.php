<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use ReyhanTeam\TelegramBotRouter\TelegramBot;

class ConversationRegistrar
{
    protected array $steps = [];
    protected int $ttl = 3600;
    protected array $middleware = [];
    protected ?string $cacheStore = null;

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

    public function middleware(array $middleware): static
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function cacheStore(?string $store): static
    {
        $this->cacheStore = $store;
        return $this;
    }

    public function startOnCommand(string $command): void
    {
        TelegramBot::addConversation($this->name, $this->steps, $this->ttl, $this->middleware, $this->cacheStore);
        TelegramBot::onCommand($command, function ($update) {
            app(ConversationManager::class)->start(
                $update,
                $this->name,
                $this->steps,
                $this->ttl,
                [],
                $this->middleware,
                $this->cacheStore
            );
        });
    }

    public function cancelOnCommand(string $command = 'cancel'): static
    {
        TelegramBot::cancelConversationOnCommand($command);
        return $this;
    }

    public function register(): void
    {
        TelegramBot::addConversation($this->name, $this->steps, $this->ttl, $this->middleware, $this->cacheStore);
    }
}
