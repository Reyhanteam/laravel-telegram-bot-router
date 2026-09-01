<?php

namespace ReyhanTeam\TelegramBotRouter\Console\Commands;

use Illuminate\Console\Command;
use ReyhanTeam\TelegramBotRouter\TelegramRouteCache;

class TelegramRouteClearCommand extends Command
{
    protected $signature = 'telegram:route:clear';
    protected $description = 'Clear the Telegram bot route cache.';

    public function handle(): int
    {
        $cleared = TelegramRouteCache::clear();

        if ($cleared) {
            $this->components->info('Telegram bot route cache cleared successfully.');
        } else {
            $this->components->warn('Telegram bot route cache was already clear.');
        }

        return self::SUCCESS;
    }
}
