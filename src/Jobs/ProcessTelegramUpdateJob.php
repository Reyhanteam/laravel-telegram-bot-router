<?php

namespace ReyhanTeam\TelegramBotRouter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReyhanTeam\TelegramBotRouter\TelegramRouter;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ProcessTelegramUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $update,
        public array $route,
    ) {
        $this->onQueue(config('telegram-bot-router.queue.queue', 'default'));
    }

    public function handle(TelegramRouter $router): void
    {
        $router->processQueuedRoute(TelegramUpdate::fromArray($this->update), $this->route);
    }
}
