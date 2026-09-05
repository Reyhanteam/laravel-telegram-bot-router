<?php

namespace ReyhanTeam\TelegramBotRouter\Jobs;

use ReyhanTeam\TelegramBotRouter\TelegramRouter;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class ProcessTelegramUpdateJob extends TelegramQueueJob
{
    public function __construct(
        public array $update,
        public array $route,
    ) {
        $this->deduplicationKey = 'telegram-bot-router.queue.update.' . (string) ($update['update_id'] ?? sha1(json_encode($update)));
        $this->configureQueue();
    }

    public function handle(TelegramRouter $router): void
    {
        $this->run(fn () => $router->processQueuedRoute(TelegramUpdate::fromArray($this->update), $this->route));
    }

    protected function queueContext(): array
    {
        return [
            'update_id' => $this->update['update_id'] ?? null,
            'route' => $this->route['name'] ?? $this->route['pattern'] ?? null,
        ];
    }
}
