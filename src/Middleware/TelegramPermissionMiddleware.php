<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class TelegramPermissionMiddleware
{
    public function handle(TelegramUpdate $update, Closure $next, string $permissions = ''): mixed
    {
        $chatId = $update->chatId();
        $userId = $update->userId();
        $required = array_values(array_filter(array_map('trim', explode(',', $permissions)), static fn ($value) => $value !== ''));

        if ($chatId === null || $userId === null || $required === []) return null;

        try {
            $member = Telegram::getChatMember([
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);

            $status = (string) ($member->status ?? '');
            if ($status === 'creator') return $next($update);

            foreach ($required as $permission) {
                if (!in_array($permission, ['creator', 'administrator'], true) && !filter_var($member->{$permission} ?? false, FILTER_VALIDATE_BOOL)) {
                    Log::info('Telegram route denied by permission condition', [
                        'chat_id' => $chatId,
                        'user_id' => $userId,
                        'permission' => $permission,
                    ]);
                    return null;
                }
            }

            if ($status === 'administrator' || $status === 'creator') return $next($update);
        } catch (Throwable $exception) {
            Log::warning('Telegram permission condition check failed', [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);
        }

        return null;
    }
}
