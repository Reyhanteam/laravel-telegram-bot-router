<?php

namespace ReyhanTeam\TelegramBotRouter\Jobs;

use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

class SendTelegramMessageJob extends TelegramQueueJob
{
    public function __construct(public array $params)
    {
        $this->configureQueue();
    }

    public function handle(TelegramApiClient $telegram): void
    {
        $this->run(fn () => $telegram->call('sendMessage', $this->params));
    }

    protected function queueContext(): array
    {
        return [
            'chat_id' => $this->params['chat_id'] ?? null,
            'method' => 'sendMessage',
        ];
    }
}
