<?php

namespace ReyhanTeam\TelegramBotRouter\Providers;

use ReyhanTeam\TelegramBotRouter\Events\CallbackQueryReceived;
use ReyhanTeam\TelegramBotRouter\Events\CommandReceived;
use ReyhanTeam\TelegramBotRouter\Events\MessageReceived;
use ReyhanTeam\TelegramBotRouter\Events\UpdateReceived;
use ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class WebhookProvider
{
    protected $router;

    public function __construct($router, $config)
    {
        $this->router = $router;
    }

    public function start()
    {
        $raw = file_get_contents('php://input');
        $update = json_decode($raw, true);

        if (!is_array($update) || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidTelegramUpdateException('Invalid JSON received from Telegram.');
        }

        if (!isset($update['update_id'])) {
            throw new InvalidTelegramUpdateException('Telegram update_id is missing.');
        }

        $telegramUpdate = TelegramUpdate::fromArray($update);

        event(new UpdateReceived($telegramUpdate));

        if (isset($telegramUpdate->message)) {
            event(new MessageReceived($telegramUpdate));

            if (
                isset($telegramUpdate->message->text)
                && str_starts_with(trim((string) $telegramUpdate->message->text), '/')
            ) {
                event(new CommandReceived($telegramUpdate));
            }
        }

        if (isset($telegramUpdate->callback_query)) {
            event(new CallbackQueryReceived($telegramUpdate));
        }

        $this->router->dispatch($telegramUpdate);
    }
}
