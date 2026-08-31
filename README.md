# Laravel Telegram Bot Router

## Version 1.0.8

> **Status:** Webhook and Polling are working in the current version.
>
> - ✅ Webhook
> - ✅ Polling
>
> Current package compatibility is defined by `composer.json`: PHP `^8.1`, Laravel `12.x` / `13.x`, and `irazasyed/telegram-bot-sdk` `^3.15`.

## About

`laravel-telegram-bot-router` is a Laravel package for routing Telegram bot updates in a Laravel-style way.

The package keeps Telegram bot routes separate from Laravel HTTP routes. Telegram routes are defined in `routes/bot.php`, while normal Laravel HTTP routes remain in `routes/web.php`.

The package can receive Telegram updates through **Webhook** or **Polling** and send each update to the matching Telegram route.

## Installation

```bash
composer require reyhanteam/laravel-telegram-bot-router
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=telegram-bot-config
```

Publish the Telegram routes:

```bash
php artisan vendor:publish --tag=telegram-bot-routes
```

This creates:

```text
routes/bot.php
```

## Configuration

The package uses `config/telegram-bot-router.php`.

```env
TELEGRAM_BOT_TOKEN=your-bot-token
TELEGRAM_BOT_MODE=webhook
TELEGRAM_WEBHOOK_PATH=/telegram/webhook
TELEGRAM_WEBHOOK_URL=https://example.com/telegram/webhook
TELEGRAM_POLLING_INTERVAL=1500
TELEGRAM_POLLING_TIMEOUT=30
```

Supported modes:

```text
webhook
polling
```

## Telegram Bot Routes

The Telegram router class is `TelegramBot`. In `routes/bot.php`, use the `BOT` alias:

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;
```

### Command

`onCommand()` accepts **two arguments**:

1. Command
2. Callback

For a controller action, pass the controller and method as one array:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

The command can include `/` or omit it:

```php
BOT::onCommand('/start', [StartController::class, 'index']);
BOT::onCommand('help', [HelpController::class, 'index']);
```

A Closure can also be used:

```php
BOT::onCommand('start', function ($update) {
    // Handle /start
});
```

### Text

`onText()` also accepts **two arguments**:

1. Text or regular expression pattern
2. Callback

Controller example:

```php
BOT::onText('hello', [MessageController::class, 'handle']);
```

Regular expressions are supported:

```php
BOT::onText('/^hello/i', [MessageController::class, 'handle']);
```

When a regular expression matches, the matches are available from `$update->matches`.

### Callback Query

Register a callback-query handler with:

```php
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

Or use a Closure:

```php
BOT::onCallbackQuery(function ($update) {
    // Handle callback query
});
```

Read callback data with:

```php
$update->callbackQueryData();
```

### Fallback

A fallback handler runs when no registered route matches the update:

```php
BOT::fallback(function ($update) {
    // Handle unmatched updates
});
```

### Invalid Updates

An invalid-update handler can be registered with:

```php
BOT::onInvalid(function ($update) {
    // Handle invalid updates
});
```

## Controllers

Controller actions use Laravel's Service Container.

Example:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

The router resolves the controller through Laravel and calls the selected method. The current Telegram update is passed as the `update` argument.

```php
class StartController
{
    public function index($update)
    {
        // $update contains the Telegram update.
    }
}
```

Laravel dependency injection can also be used:

```php
public function index(MyService $service, $update)
{
    // ...
}
```

## TelegramUpdate

Telegram updates are wrapped by `TelegramUpdate`.

Useful methods:

```php
$update->chatId();
$update->userId();
$update->messageId();
$update->text();
$update->callbackQueryData();
$update->originalUpdate();
```

Nested properties are also available:

```php
$update->message->chat->id;
$update->message->from->id;
$update->message->text;
```

## Webhook

In webhook mode, the configured webhook path is registered in Laravel and incoming Telegram updates are passed to the Telegram router.

Default path:

```text
POST /telegram/webhook
```

The route can also be registered with:

```bash
php artisan reyhan:setWebhookRoute
```

## Polling

Polling continuously requests Telegram updates and sends them to the same Telegram router.

Start polling with:

```bash
php artisan reyhan:start-polling
```

Polling uses the configured interval, long-polling timeout, and Telegram API URL.

## Architecture

```text
Telegram
   │
   ├── Webhook ──► Laravel Route ──┐
   │                               │
   └── Polling ────────────────────┤
                                   ▼
                            TelegramRouter
                                   │
                                   ▼
                              TelegramBot
                                   │
                    ┌──────────────┼──────────────┐
                    ▼              ▼              ▼
                Command          Text       CallbackQuery
                    │              │              │
                    └──────────────┼──────────────┘
                                   ▼
                               Controller
```

The goal is to give Telegram bots their own routing layer without mixing bot logic with `routes/web.php`.

# Feature Roadmap

The roadmap is ordered by implementation priority. The top items should be implemented first because later features depend on them.

## 🔴 Priority 1 — Core Telegram Routing

### Current

- [x] Webhook support
- [x] Polling support
- [x] `routes/bot.php`
- [x] `BOT::onCommand()`
- [x] `BOT::onText()`
- [x] `BOT::onCallbackQuery()`
- [x] `BOT::fallback()`
- [x] `BOT::onInvalid()`
- [x] Closure handlers
- [x] Controller + method handlers
- [x] Laravel Service Container controller resolution
- [x] Dependency Injection for controller methods
- [x] Regular expression matching for text routes
- [x] `TelegramUpdate` wrapper

### Planned improvements

- [ ] Better route matching
- [ ] Route constraints
- [ ] Command arguments
- [ ] Route parameters

## 🔴 Priority 2 — Middleware ⭐

Middleware is one of the most important planned features.

The router should support Laravel-style Telegram middleware.

Planned API direction:

```php
BOT::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

Planned features:

- [ ] Telegram middleware
- [ ] Route middleware
- [ ] Global Telegram middleware
- [ ] Middleware groups
- [ ] Middleware parameters
- [ ] Middleware pipeline

Use cases include authentication, admin access, permission checks, registration checks, logging, and rate limiting.

## 🔴 Priority 3 — Route Groups

Add Laravel-style groups for Telegram routes.

- [ ] Route groups
- [ ] Shared middleware
- [ ] Nested groups
- [ ] Shared route options

Example target API:

```php
BOT::group([
    'middleware' => [AdminMiddleware::class],
], function () {
    BOT::onCommand('users', [UserController::class, 'index']);
    BOT::onCommand('stats', [StatsController::class, 'index']);
});
```

## 🔴 Priority 4 — Conversation / State / Wait for Next Message ⭐⭐⭐

This is a major feature for interactive Telegram bots.

The bot must be able to ask the user for information and then wait for the user's next message.

Example:

```text
Bot: Please enter your name.
User: Hossein
Bot: Please enter your phone number.
User: 0912...
```

The router must remember the user's current state. When the next update arrives, it must be possible to route that update to the current conversation step instead of treating it as an unrelated normal message.

Planned features:

- [ ] Per-user conversation state
- [ ] Conversation steps
- [ ] Wait for the next message
- [ ] Save the current step
- [ ] Move to the next step
- [ ] Finish a conversation
- [ ] Cancel a conversation
- [ ] Conversation timeout
- [ ] Conversation data
- [ ] Input validation
- [ ] Persistent state using Laravel Cache or another configurable storage driver

Possible API direction:

```php
BOT::conversation('register')
    ->step(1, [RegisterController::class, 'name'])
    ->step(2, [RegisterController::class, 'phone'])
    ->step(3, [RegisterController::class, 'finish']);
```

Another possible design:

```php
BOT::onCommand('register', [RegisterController::class, 'start'])
    ->waitForNextMessage([RegisterController::class, 'name']);
```

The exact API will be decided during implementation. The requirement is that the router can remember that a user is waiting for the next message.

## 🔴 Priority 5 — Exception and Error Handling

- [ ] Telegram route exceptions
- [ ] Invalid update exceptions
- [ ] Telegram API exceptions
- [ ] Configurable Telegram exception handler
- [ ] Safe logging
- [ ] Never expose bot tokens in logs

Possible exception classes:

```text
TelegramRouteNotFoundException
TelegramInvalidUpdateException
TelegramApiException
```

## 🟠 Priority 6 — Events

Integrate Telegram routing with Laravel Events.

- [ ] Update received event
- [ ] Message received event
- [ ] Command received event
- [ ] Callback query event
- [ ] Route matched event
- [ ] Route dispatched event

Possible event names:

```text
TelegramUpdateReceived
TelegramMessageReceived
TelegramCommandReceived
TelegramCallbackReceived
TelegramRouteMatched
```

## 🟠 Priority 7 — Rate Limiting

Protect bots from message spam and excessive requests.

- [ ] Per-user limits
- [ ] Per-chat limits
- [ ] Per-command limits
- [ ] Configurable limits
- [ ] Laravel Cache integration

## 🟠 Priority 8 — Telegram Route List

Provide a command similar to Laravel's `route:list`:

```bash
php artisan telegram:route:list
```

Example target output:

```text
COMMAND     /start       StartController@index
COMMAND     /help        HelpController@index
CALLBACK    profile      ProfileController@show
TEXT        hello        MessageController@handle
```

## 🟠 Priority 9 — Queue Support

Support Laravel queues for long-running or expensive Telegram operations.

- [ ] Queue update processing
- [ ] Queue message sending
- [ ] Queue heavy bot tasks
- [ ] Laravel queue integration

This is especially useful for webhook mode because webhook requests should finish quickly.

## 🟡 Priority 10 — Named Telegram Routes

Add route names.

Example target API:

```php
BOT::onCommand('start', [StartController::class, 'index'])
    ->name('telegram.start');
```

## 🟡 Priority 11 — Telegram Route Cache

Support production route caching.

```bash
php artisan telegram:route:cache
php artisan telegram:route:clear
```

## 🟡 Priority 12 — More Telegram Update Types

Support more Telegram Bot API update types.

- [ ] Inline Query
- [ ] Edited Message
- [ ] Channel Post
- [ ] Edited Channel Post
- [ ] Chat Member
- [ ] My Chat Member
- [ ] Chat Join Request
- [ ] Other future Telegram update types

## 🟡 Priority 13 — Better Callback Query Routing

The current router accepts callback-query handlers. Future versions should support more precise callback-data matching.

- [ ] Exact callback data matching
- [ ] Regular expression callback matching
- [ ] Callback route parameters
- [ ] Named callback routes
- [ ] Better inline keyboard integration

## 🟡 Priority 14 — User and Chat Conditions

Support conditions based on Telegram users and chats.

- [ ] Admin-only routes
- [ ] User conditions
- [ ] Private-chat conditions
- [ ] Group-chat conditions
- [ ] Channel conditions
- [ ] User permission checks
- [ ] Chat type constraints

## 🟡 Priority 15 — Testing Tools

Provide testing helpers similar to Laravel HTTP testing tools.

- [ ] Telegram fake
- [ ] Fake Telegram updates
- [ ] Route dispatch assertions
- [ ] Message sending assertions
- [ ] Callback assertions
- [ ] Conversation/state tests

Possible target API:

```php
BOT::fake();

// Dispatch a fake Telegram update...

BOT::assertRoutedTo(StartController::class, 'index');
```

## Design Principles

1. Keep Telegram routes separate from Laravel HTTP routes.
2. Use Laravel's Service Container for controller resolution.
3. Keep the public routing API simple.
4. Keep route registration separate from update processing.
5. Support Webhook and Polling through the same routing layer.
6. Follow PSR-4 autoloading.
7. Keep code clean and maintainable.
8. Keep Telegram-specific routing logic out of `routes/web.php`.
9. Build advanced features such as Middleware and Conversation State on top of the core router.

## Current Public API

The current public routing API is:

```php
BOT::onCommand($command, $callback);
BOT::onText($pattern, $callback);
BOT::onCallbackQuery($callback);
BOT::fallback($callback);
BOT::onInvalid($callback);
```

For controller actions, `$callback` is:

```php
[ControllerClass::class, 'method']
```

Example:

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;

BOT::onCommand('start', [StartController::class, 'index']);
BOT::onText('hello', [MessageController::class, 'handle']);
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

This section describes the API that exists in the current code. Roadmap APIs are only proposals until implemented.
