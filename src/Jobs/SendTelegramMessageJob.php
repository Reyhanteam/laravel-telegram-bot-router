<?php

namespace ReyhanTeam\TelegramBotRouter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $params)
    {
        $this->onQueue(config('telegram-bot-router.queue.queue', 'default'));
    }

    public function handle(): void
    {
        Telegram::sendMessage($this->params);
    }
}
