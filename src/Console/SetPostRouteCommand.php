<?php

namespace ReyhanTeam\TelegramBotRouter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReyhanTeam\TelegramBotRouter\Core\UpdateManager;

class SetPostRouteCommand extends Command
{
    protected $signature = 'reyhan:setWebhookRoute';

    protected $description = 'Register Telegram webhook route from configuration';

    public function handle(): int
    {
        $path = config('telegram-bot-router.webhook.path', '/telegram/webhook');
        $path = '/'.ltrim($path, '/');

        Route::post($path, [UpdateManager::class, 'handleWebhook']);

        $this->info('Telegram webhook route registered:');
        $this->line("POST {$path}");

        return self::SUCCESS;
    }
}
