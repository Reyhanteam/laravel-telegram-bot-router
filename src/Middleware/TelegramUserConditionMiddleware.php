<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class TelegramUserConditionMiddleware
{
    public function handle(TelegramUpdate $update, Closure $next, string $userIds = ''): mixed
    {
        $allowed = array_filter(array_map('trim', explode(',', $userIds)), static fn ($id) => $id !== '');
        if (in_array((string) $update->userId(), $allowed, true)) return $next($update);
        Log::info('Telegram route denied by user condition', ['user_id' => $update->userId()]);
        return null;
    }
}
