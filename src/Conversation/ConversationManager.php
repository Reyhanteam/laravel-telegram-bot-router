<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use Closure;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ConversationManager
{
    protected string $prefix = 'telegram_bot_router.conversation.';

    public function start(TelegramUpdate $update, string $name, array $steps, int $ttl = 3600, array $data = []): void
    {
        $this->put($update, [
            'name' => $name,
            'steps' => $steps,
            'step' => 0,
            'data' => $data,
            'ttl' => $ttl,
        ], $ttl);
    }

    public function active(TelegramUpdate $update): bool
    {
        return $this->get($update) !== null;
    }

    public function handle(TelegramUpdate $update): bool
    {
        $conversation = $this->get($update);

        if ($conversation === null) {
            return false;
        }

        $steps = $conversation['steps'] ?? [];
        $index = (int) ($conversation['step'] ?? 0);

        if (!isset($steps[$index])) {
            $this->forget($update);
            return false;
        }

        $result = $this->resolveAction($steps[$index], $update, $conversation['data'] ?? []);

        if (is_array($result) && array_key_exists('data', $result)) {
            $conversation['data'] = $result['data'];
        }

        if (is_array($result) && ($result['done'] ?? false) === true) {
            $this->forget($update);
            return true;
        }

        $conversation['step'] = $index + 1;

        if (!isset($steps[$conversation['step']])) {
            $this->forget($update);
            return true;
        }

        $this->put($update, $conversation, $this->conversationTtl($conversation));
        return true;
    }

    public function get(TelegramUpdate $update): ?array
    {
        $value = cache()->get($this->key($update));
        return is_array($value) ? $value : null;
    }

    public function data(TelegramUpdate $update): array
    {
        return $this->get($update)['data'] ?? [];
    }

    public function forget(TelegramUpdate $update): void
    {
        cache()->forget($this->key($update));
    }

    protected function put(TelegramUpdate $update, array $conversation, int $ttl): void
    {
        cache()->put($this->key($update), $conversation, $ttl);
    }

    protected function key(TelegramUpdate $update): string
    {
        return $this->prefix . ($update->chatId() ?? 'unknown') . '.' . ($update->userId() ?? 'unknown');
    }

    protected function conversationTtl(array $conversation): int
    {
        return (int) ($conversation['ttl'] ?? config('telegram-bot-router.conversation.ttl', 3600));
    }

    protected function resolveAction($action, TelegramUpdate $update, array $data): mixed
    {
        if ($action instanceof Closure) {
            return $action($update, $data);
        }

        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            $instance = app()->make($controller);

            return app()->call([$instance, $method], [
                'update' => $update,
                'data' => $data,
            ]);
        }

        throw new \InvalidArgumentException('Invalid Telegram conversation step action.');
    }
}
