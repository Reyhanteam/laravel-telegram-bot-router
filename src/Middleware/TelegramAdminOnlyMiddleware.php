<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class TelegramAdminOnlyMiddleware
{
    public function handle(TelegramUpdate $update, Closure $next): mixed
    {
        $chatId = $update->chatId();
        $userId = $update->userId();

        if ($chatId === null || $userId === null) return null;

        try {
            $member = Telegram::getChatMember([
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);

            $status = (string) ($member->status ?? '');
            if (in_array($status, ['creator', 'administrator'], true)) return $next($update);
        } catch (Throwable $exception) {
            Log::warning('Telegram admin condition check failed', [
                'chat_id' => $chatId,
                'user_id' => $userId,
                'error' => $exception->getMessage(),
            ]);
        }

        Log::info('Telegram route denied by admin condition', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
        return null;
    }
}
