<?php

namespace ReyhanTeam\TelegramBotRouter\Conversation;

use Closure;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationCancelled;
use ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationFinished;
use ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationStarted;
use ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationStepCompleted;
use ReyhanTeam\TelegramBotRouter\TelegramBot;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use ReyhanTeam\TelegramBotRouter\Middleware\MiddlewarePipeline;

class ConversationManager
{
    protected string $prefix = 'telegram_bot_router.conversation.';

    public function start(TelegramUpdate $update, string $name, array $steps, int $ttl = 3600, array $data = [], array $middleware = [], ?string $cacheStore = null): void
    {
        $conversation = [
            'name' => $name,
            'step' => 0,
            'data' => $data,
            'ttl' => $ttl,
            'middleware' => $middleware,
            'cache_store' => $cacheStore,
            'expires_at' => time() + $ttl,
        ];

        $this->put($update, $conversation, $ttl, $cacheStore);
        event(new ConversationStarted($update, $name, $data));
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

        $name = (string) ($conversation['name'] ?? '');
        $steps = $this->steps($name);
        $index = (int) ($conversation['step'] ?? 0);

        if (!isset($steps[$index])) {
            $this->forget($update, $conversation);
            event(new ConversationFinished($update, $name, $conversation['data'] ?? []));
            return false;
        }

        try {
            $result = (new MiddlewarePipeline($conversation['middleware'] ?? []))->process(
                $update,
                fn (TelegramUpdate $update): mixed => $this->resolveAction(
                    $steps[$index],
                    $update,
                    $conversation['data'] ?? []
                )
            );
        } catch (InvalidArgumentException $exception) {
            Log::warning('Conversation input validation failed', [
                'conversation' => $name,
                'step' => $index,
                'message' => $exception->getMessage(),
            ]);

            // Keep the current step active. The user can send another input
            // and the same validation step will be executed again.
            $this->put(
                $update,
                $conversation,
                $this->conversationTtl($conversation),
                $conversation['cache_store'] ?? null
            );

            return true;
        }

        if (is_array($result) && array_key_exists('data', $result)) {
            $conversation['data'] = $result['data'];
        }

        event(new ConversationStepCompleted(
            $update,
            $name,
            $index,
            $conversation['data'] ?? []
        ));

        if (is_array($result) && ($result['done'] ?? false) === true) {
            $this->forget($update, $conversation);
            event(new ConversationFinished($update, $name, $conversation['data'] ?? []));
            return true;
        }

        $conversation['step'] = $index + 1;

        if (!isset($steps[$conversation['step']])) {
            $this->forget($update, $conversation);
            event(new ConversationFinished($update, $name, $conversation['data'] ?? []));
            return true;
        }

        $this->put($update, $conversation, $this->conversationTtl($conversation), $conversation['cache_store'] ?? null);
        return true;
    }

    public function get(TelegramUpdate $update): ?array
    {
        $value = $this->cache()->get($this->key($update));
        return is_array($value) ? $value : null;
    }

    public function data(TelegramUpdate $update): array
    {
        return $this->get($update)['data'] ?? [];
    }

    public function cancel(TelegramUpdate $update): bool
    {
        $conversation = $this->get($update);
        if ($conversation === null) return false;
        $this->forget($update, $conversation);
        event(new ConversationCancelled($update, (string) ($conversation['name'] ?? ''), $conversation['data'] ?? []));
        return true;
    }

    public function forget(TelegramUpdate $update, ?array $conversation = null): void
    {
        $store = $conversation['cache_store'] ?? null;
        $this->cache($store)->forget($this->key($update));
    }

    protected function put(TelegramUpdate $update, array $conversation, int $ttl, ?string $store = null): void
    {
        $this->cache($store)->put($this->key($update), $conversation, $ttl);
    }

    protected function cache(?string $store = null)
    {
        $store ??= config('telegram-bot-router.conversation.cache_store');
        return $store ? cache()->store($store) : cache();
    }

    protected function key(TelegramUpdate $update): string
    {
        return $this->prefix . ($update->chatId() ?? 'unknown') . '.' . ($update->userId() ?? 'unknown');
    }

    protected function conversationTtl(array $conversation): int
    {
        return max(1, (int) ($conversation['ttl'] ?? config('telegram-bot-router.conversation.ttl', 3600)));
    }

    protected function resolveAction($action, TelegramUpdate $update, array $data): mixed
    {
        $input = new ConversationInput($update, $data);

        if ($action instanceof Closure) {
            return app()->call($action, [
                'update' => $update,
                'input' => $input,
                'data' => $data,
            ]);
        }

        if (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            $instance = app()->make($controller);

            return app()->call([$instance, $method], [
                'update' => $update,
                'data' => $data,
                'input' => $input,
            ]);
        }

        throw new InvalidArgumentException('Invalid Telegram conversation step action.');
    }

    protected function steps(string $name): array
    {
        $conversation = TelegramBot::getConversations()[$name] ?? null;
        return is_array($conversation) ? ($conversation['steps'] ?? []) : [];
    }
}
