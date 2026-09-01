<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramRateLimitMiddleware;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class TelegramRateLimitMiddlewareTest extends TestCase
{
    public function test_user_limit_blocks_after_max_attempts(): void
    {
        $update = new TelegramUpdate([
            'update_id' => 1,
            'message' => [
                'chat' => ['id' => 100],
                'from' => ['id' => 10],
                'text' => 'hello',
            ],
        ]);

        RateLimiter::clear('telegram_bot_router.rate_limit.user.10');

        $middleware = new TelegramRateLimitMiddleware([
            'user' => [
                'max_attempts' => 2,
                'decay_seconds' => 60,
            ],
        ]);

        $calls = 0;
        $next = function () use (&$calls): string {
            $calls++;

            return 'ok';
        };

        $this->assertSame('ok', $middleware->handle($update, $next));
        $this->assertSame('ok', $middleware->handle($update, $next));
        $this->assertNull($middleware->handle($update, $next));
        $this->assertSame(2, $calls);
    }

    public function test_command_limit_is_separated_by_user(): void
    {
        $firstUser = new TelegramUpdate([
            'message' => [
                'chat' => ['id' => 100],
                'from' => ['id' => 10],
                'text' => '/start',
            ],
        ]);

        $secondUser = new TelegramUpdate([
            'message' => [
                'chat' => ['id' => 200],
                'from' => ['id' => 20],
                'text' => '/start',
            ],
        ]);

        RateLimiter::clear('telegram_bot_router.rate_limit.command.start.10');
        RateLimiter::clear('telegram_bot_router.rate_limit.command.start.20');

        $middleware = new TelegramRateLimitMiddleware([
            'command' => [
                'max_attempts' => 1,
                'decay_seconds' => 60,
            ],
        ]);

        $next = static fn (): string => 'ok';

        $this->assertSame('ok', $middleware->handle($firstUser, $next));
        $this->assertNull($middleware->handle($firstUser, $next));
        $this->assertSame('ok', $middleware->handle($secondUser, $next));
    }
}
