<?php

namespace ReyhanTeam\TelegramBotRouter\Console\Commands;

use Illuminate\Console\Command;
use ReyhanTeam\TelegramBotRouter\TelegramBot;
use ReyhanTeam\TelegramBotRouter\TelegramRouteCache;

class TelegramRouteCacheCommand extends Command
{
    protected $signature = 'telegram:route:cache';
    protected $description = 'Cache Telegram bot route lookup data.';

    public function handle(): int
    {
        $routes = TelegramBot::getRoutes();
        $compiled = TelegramRouteCache::cache();

        $this->components->info('Telegram bot routes cached successfully.');
        $this->components->twoColumnDetail('Routes', (string) count($routes));
        $this->components->twoColumnDetail('Exact commands', (string) count($compiled['exact_commands'] ?? []));
        $this->components->twoColumnDetail('Exact text routes', (string) count($compiled['exact_text'] ?? []));
        $this->components->twoColumnDetail('Exact callback routes', (string) count($compiled['exact_callbacks'] ?? []));

        return self::SUCCESS;
    }
}
