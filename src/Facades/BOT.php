<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Facades;

use Illuminate\Support\Facades\Facade;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Testing\TelegramFake;

/**
 * Developer-friendly Telegram Bot API facade.
 *
 * @see https://core.telegram.org/bots/api
 */
final class BOT extends Facade
{
    use TelegramApiAdditionalMethods;

    protected static function getFacadeAccessor(): string
    {
        return TelegramApiClient::class;
    }

    public static function fake(): TelegramFake
    {
        $fake = new TelegramFake();
        static::swap($fake);

        return $fake;
    }

    private static function dispatch(string $method, array $arguments): mixed
    {
        $client = static::getFacadeRoot();

        if ($client instanceof TelegramFake) {
            return $client->{$method}(...$arguments);
        }

        if (!$client instanceof TelegramApiClient) {
            throw new \RuntimeException('Telegram API client is not available.');
        }

        return $client->__call($method, $arguments);
    }

