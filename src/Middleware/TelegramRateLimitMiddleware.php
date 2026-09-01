<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class TelegramRateLimitMiddleware
{
    public function __construct(protected array $limits = [], protected ?array $route = null)
    {
    }

    public function handle(TelegramUpdate $update, Closure $next): mixed
    {
        foreach ($this->limits as $scope => $limit) {
            if (!is_array($limit) || !($limit['enabled'] ?? true)) {
                continue;
            }

            $maxAttempts = max(1, (int) ($limit['max_attempts'] ?? 60));
            $decaySeconds = max(1, (int) ($limit['decay_seconds'] ?? 60));

            if (! $this->appliesTo($scope, $update)) {
                continue;
            }

            $key = $this->key($scope, $update);

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                return null;
            }

            RateLimiter::hit($key, $decaySeconds);
        }

        return $next($update);
    }

    protected function appliesTo(string $scope, TelegramUpdate $update): bool
    {
        return match ($scope) {
            'user' => $update->userId() !== null,
            'chat' => $update->chatId() !== null,
            'command' => $this->command($update) !== null,
            default => false,
        };
    }

    protected function key(string $scope, TelegramUpdate $update): string
    {
        $prefix = (string) config('telegram-bot-router.rate_limit.prefix', 'telegram_bot_router.rate_limit');

        return match ($scope) {
            'user' => $prefix.'.user.'.$update->userId(),
            'chat' => $prefix.'.chat.'.$update->chatId(),
            'command' => $prefix.'.command.'.$this->command($update).'.'.($update->userId() ?? $update->chatId()),
            default => $prefix.'.unknown',
        };
    }

    protected function command(TelegramUpdate $update): ?string
    {
        $text = $update->text();

        if ($text === null || !str_starts_with(trim($text), '/')) {
            return null;
        }

        $command = preg_split('/\s+/', trim($text), 2)[0] ?? '';

        if (str_contains($command, '@')) {
            $command = explode('@', $command, 2)[0];
        }

        return strtolower(ltrim($command, '/')) ?: null;
    }
}
