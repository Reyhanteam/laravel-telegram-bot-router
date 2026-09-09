# Telegram Response API

The package provides a high-level fluent response builder in `TelegramResponse`.

## Basic response

```php
use ReyhanTeam\TelegramBotRouter\Facades\Telegram;

return Telegram::response($chatId)
    ->message('Hello')
    ->parseMode('HTML');
```

When a `TelegramResponse` is returned by a Telegram controller or closure, the router sends it automatically.

## Reply markup and replies

```php
return Telegram::response($chatId)
    ->message('Choose an option')
    ->replyMarkup([
        'inline_keyboard' => [
            [
                ['text' => 'Profile', 'callback_data' => 'profile'],
            ],
        ],
    ])
    ->replyTo($messageId);
```

## Media

```php
return Telegram::response($chatId)
    ->photo($photo, ['caption' => 'Photo'])
    ->parseMode('HTML');
```

The same fluent API supports `audio()`, `document()`, `video()`, and `animation()`.

## Edit and delete

```php
return Telegram::response($chatId)
    ->editMessage($messageId, 'Updated');

return Telegram::response($chatId)
    ->deleteMessage($messageId);
```

## Queue a response

A response can be serialized into a queue job without serializing the HTTP client:

```php
Telegram::response($chatId)
    ->message('This is queued')
    ->queue('telegram-high');
```

The queued job is `SendTelegramResponseJob` and resolves the current `TelegramApiClient` inside the worker.

## Generic Telegram methods

For methods that do not have a dedicated helper, use `method()` or `option()`:

```php
return Telegram::response($chatId)
    ->method('sendLocation', [
        'latitude' => 35.7,
        'longitude' => 51.4,
    ]);
```
