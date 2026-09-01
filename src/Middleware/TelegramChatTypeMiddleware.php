<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class TelegramChatTypeMiddleware
{
    public function handle(TelegramUpdate $update, Closure $next, string $types = ''): mixed
    {
        $allowed = array_filter(array_map('trim', explode(',', $types)), static fn ($type) => $type !== '');
        $chatType = $this->chatType($update);

        if (in_array($chatType, $allowed, true)) return $next($update);

        Log::info('Telegram route denied by chat type condition', [
            'chat_type' => $chatType,
            'allowed' => $allowed,
        ]);
        return null;
    }

    protected function chatType(TelegramUpdate $update): ?string
    {
        foreach (['message', 'callback_query.message', 'edited_message', 'channel_post', 'edited_channel_post', 'chat_member', 'my_chat_member', 'chat_join_request'] as $path) {
            $value = $this->nested($update, $path);
            if ($value !== null) return $this->nested($update, $path . '.chat.type');
        }

        return null;
    }

    protected function nested(TelegramUpdate $update, string $path): mixed
    {
        $value = $update;
        foreach (explode('.', $path) as $part) {
            if ($value === null || !isset($value->{$part})) return null;
            $value = $value->{$part};
        }
        return $value;
    }
}
