# Laravel Telegram Bot Router

## Version 1.0.8

> **Status:** Webhook, Polling, Telegram Middleware, and Conversation State are working in the current development version.

- ✅ Webhook
- ✅ Polling
- ✅ `routes/bot.php`
- ✅ `BOT::onCommand()`
- ✅ `BOT::onText()`
- ✅ `BOT::onCallbackQuery()`
- ✅ `BOT::fallback()`
- ✅ `BOT::onInvalid()`
- ✅ Route middleware
- ✅ Global middleware
- ✅ Middleware pipeline
- ✅ Middleware groups
- ✅ Nested middleware groups
- ✅ Middleware parameters
- ✅ Conversation state
- ✅ Per-user conversation storage
- ✅ Conversation steps
- ✅ Wait for next message
- ✅ Conversation timeout
- ✅ Conversation data

## About

`laravel-telegram-bot-router` is a Laravel package for routing Telegram bot updates in a Laravel-style way.

Telegram routes are kept separate from Laravel HTTP routes:

```text
routes/web.php  -> Laravel HTTP routes
routes/bot.php  -> Telegram bot routes
```

The package supports both Telegram update modes:

- **Webhook:** Telegram sends updates to Laravel.
- **Polling:** The package continuously requests updates from Telegram.

Both modes use the same Telegram routing layer.

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

## Basic Routing

Use the `BOT` alias in `routes/bot.php`:

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;
```

### Commands

`onCommand()` receives two arguments:

```php
BOT::onCommand('start', [StartController::class, 'index']);
BOT::onCommand('/help', [HelpController::class, 'index']);
```

The package normalizes the optional `/` prefix.

### Text

`onText()` receives two arguments:

```php
BOT::onText('hello', [MessageController::class, 'handle']);
```

Regular expressions are supported:

```php
BOT::onText('/^hello/i', [MessageController::class, 'handle']);
```

Regex matches are available through `$update->matches`.

### Callback Query

```php
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

Callback data can be read with:

```php
$update->callbackQueryData();
```

### Fallback

```php
BOT::fallback(function ($update) {
    // Handle unmatched updates.
});
```

### Invalid Updates

```php
BOT::onInvalid(function ($update) {
    // Handle invalid updates.
});
```

## Controllers

Controller actions use Laravel's Service Container.

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

The Telegram update is injected as the `update` argument. Other dependencies can also be resolved by Laravel.

```php
public function index(MyService $service, $update)
{
    // ...
}
```

## TelegramUpdate

Telegram updates are wrapped by `TelegramUpdate`.

Useful methods include:

```php
$update->chatId();
$update->userId();
$update->messageId();
$update->text();
$update->callbackQueryData();
$update->originalUpdate();
```

Nested Telegram properties can also be accessed:

```php
$update->message->chat->id;
$update->message->from->id;
$update->message->text;
```

# Middleware

The Telegram middleware system follows the Laravel middleware pattern.

Execution order:

```text
Telegram Update
      ↓
Global Middleware
      ↓
Group Middleware
      ↓
Route Middleware
      ↓
Controller / Closure
```

## Creating a Middleware

You can create a Telegram middleware inside your Laravel application. A common location is:

```text
app/Telegram/Middleware/
```

For example:

```text
app/Telegram/Middleware/AdminMiddleware.php
```

Example middleware:

```php
<?php

namespace App\Telegram\Middleware;

use Closure;

class AdminMiddleware
{
    private const ADMIN_CHAT_ID = 123456789;

    public function handle($update, Closure $next)
    {
        $chatId = $update->chatId();

        if ($chatId !== self::ADMIN_CHAT_ID) {
            return null;
        }

        return $next($update);
    }
}
```

Replace `123456789` with the Telegram Chat ID that should have admin access.

### How it works

The middleware receives two main values:

```php
public function handle($update, Closure $next)
```

- `$update` is the current `TelegramUpdate` object.
- `$next` is the next step in the middleware pipeline.

If the request is allowed, call:

```php
return $next($update);
```

This continues processing.

If the request must be blocked, do not call `$next()`:

```php
return null;
```

This stops the pipeline. The controller or next middleware will not run.

## Admin Middleware Example

A typical admin-only route can be written as:

```php
use App\Http\Controllers\Telegram\AdminController;
use App\Telegram\Middleware\AdminMiddleware;
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;

BOT::middleware([
    AdminMiddleware::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

The flow is:

```text
Telegram Update
      ↓
AdminMiddleware
      ↓
Is Chat ID the admin Chat ID?
      │
   ┌──┴──┐
  Yes    No
   │      │
   ▼      ▼
 $next   STOP
   │
   ▼
AdminController
```

This is useful for admin panels, management commands, configuration commands, and other protected bot features.

> **Security note:** Keep the admin Chat ID in Laravel configuration or environment variables for production applications. Do not hard-code secrets or bot tokens in middleware.

For example:

```env
TELEGRAM_ADMIN_CHAT_ID=123456789
```

Then use configuration instead of a hard-coded value:

```php
$adminChatId = config('telegram-bot.admin_chat_id');
```

## Middleware Contract

A middleware can optionally implement the package contract:

```php
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareInterface;

class CheckUser implements TelegramMiddlewareInterface
{
    public function handle($update, Closure $next, ...$parameters)
    {
        return $next($update);
    }
}
```

The contract is optional. A class with a compatible `handle()` method can also be used.

## Route Middleware

```php
BOT::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

Text and callback routes are also supported.

## Global Middleware

```php
BOT::globalMiddleware([
    LogTelegramUpdate::class,
    CheckBotState::class,
]);
```

Global middleware runs before group and route middleware.

## Middleware Groups

```php
BOT::group([
    AdminMiddleware::class,
    LogTelegramUpdate::class,
], function () {
    BOT::onCommand('users', [UserController::class, 'index']);
    BOT::onCommand('stats', [StatsController::class, 'index']);
});
```

Nested groups are supported. Inner middleware is added after outer middleware.

## Middleware Parameters

```php
BOT::middleware([
    HasPermission::class . ':users.create',
])->onCommand('create-user', [UserController::class, 'create']);
```

A middleware receives parameters after `$next` through `...$parameters`.

# Conversation / State

Conversation State lets a bot wait for a user's next message.

Example:

```text
Bot: Please enter your name.
User: Hossein
Bot: Please enter your phone number.
User: 0912...
```

The package stores the active conversation separately for each Telegram chat and user. When the next Telegram update arrives, the active conversation is handled before normal route matching.

## Starting a Conversation

Define the steps and start the conversation from a command:

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->step([RegisterController::class, 'finish'])
    ->startOnCommand('register');
```

Now:

```text
/register
   ↓
name()
   ↓
wait for next message
   ↓
phone()
   ↓
wait for next message
   ↓
finish()
```

A Closure can also be used:

```php
BOT::conversation('register')
    ->step(function ($update, $data) {
        return ['data' => ['name' => $update->text()]];
    })
    ->step(function ($update, $data) {
        return ['done' => true, 'data' => $data];
    })
    ->startOnCommand('register');
```

## Conversation Data

A step can return updated conversation data:

```php
return [
    'data' => [
        'name' => $update->text(),
    ],
];
```

The next step receives the data as its second argument:

```php
public function phone($update, array $data)
{
    $name = $data['name'] ?? null;
}
```

## Conversation Completion

A step can finish the conversation by returning:

```php
return [
    'done' => true,
];
```

The conversation state is then removed.

## Conversation Timeout

The default conversation lifetime is configurable:

```env
TELEGRAM_CONVERSATION_TTL=3600
```

Or per conversation:

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->ttl(1800)
    ->startOnCommand('register');
```

Conversation state uses Laravel Cache. This allows the application to use its configured cache backend.

# Webhook

Webhook mode receives Telegram updates through the configured Laravel route.

Default endpoint:

```text
POST /telegram/webhook
```

Register the webhook route with:

```bash
php artisan reyhan:setWebhookRoute
```

# Polling

Start polling with:

```bash
php artisan reyhan:start-polling
```

Polling continuously requests updates from Telegram and dispatches them through the same router used by Webhook mode.

# Architecture

```text
                         Telegram
                            │
                 ┌──────────┴──────────┐
                 │                     │
              Webhook               Polling
                 │                     │
                 └──────────┬──────────┘
                            ▼
                    Telegram Router
                            │
                     Conversation Check
                            │
                 ┌──────────┴──────────┐
                 │                     │
              Active                Inactive
           Conversation                │
                 │                     ▼
                 │               Route Matching
                 │                     │
                 └──────────┬──────────┘
                            ▼
                  Global Middleware
                            │
                            ▼
                   Group Middleware
                            │
                            ▼
                   Route Middleware
                            │
                            ▼
                 Controller / Closure
```

# Feature Roadmap

The roadmap is ordered by implementation priority.

## 🔴 Priority 1 — Core Telegram Routing

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

Planned improvements:

- [ ] Better route matching
- [ ] Route constraints
- [ ] Command arguments
- [ ] Route parameters

## 🔴 Priority 2 — Middleware ⭐

- [x] Middleware pipeline
- [x] Route middleware
- [x] Global Telegram middleware
- [x] Laravel Container resolution
- [x] Middleware objects
- [x] Middleware short-circuit
- [x] Middleware execution order
- [x] Optional `TelegramMiddlewareInterface`
- [x] Middleware groups
- [x] Nested middleware groups
- [x] Middleware parameters

Planned:

- [ ] Named middleware aliases
- [ ] Middleware configuration

## 🔴 Priority 3 — Conversation / State / Wait for Next Message ⭐⭐⭐

- [x] Per-user conversation state
- [x] Conversation steps
- [x] Wait for next message
- [x] Save current step
- [x] Move to next step
- [x] Finish conversation
- [x] Conversation timeout
- [x] Conversation data
- [x] Laravel Cache storage
- [x] Controller and Closure conversation steps

Planned improvements:

- [ ] Cancel conversation command/API
- [ ] Input validation helpers
- [ ] Explicit conversation middleware
- [ ] Conversation events
- [ ] More storage driver controls

## 🟠 Priority 4 — Exception and Error Handling

- [ ] Telegram route exceptions
- [ ] Invalid update exceptions
- [ ] Telegram API exceptions
- [ ] Configurable exception handler
- [ ] Safe logging
- [ ] Never expose bot tokens in logs

## 🟠 Priority 5 — Events

- [ ] Update received event
- [ ] Message received event
- [ ] Command received event
- [ ] Callback query event
- [ ] Conversation started event
- [ ] Conversation step event
- [ ] Conversation finished event
- [ ] Route matched event

## 🟠 Priority 6 — Rate Limiting

- [ ] Per-user limits
- [ ] Per-chat limits
- [ ] Per-command limits
- [ ] Configurable limits
- [ ] Laravel Cache integration

## 🟠 Priority 7 — Telegram Route List

```bash
php artisan telegram:route:list
```

## 🟠 Priority 8 — Queue Support

- [ ] Queue update processing
- [ ] Queue message sending
- [ ] Queue heavy bot tasks
- [ ] Laravel queue integration

## 🟡 Priority 9 — Named Telegram Routes

```php
BOT::onCommand('start', [StartController::class, 'index'])
    ->name('telegram.start');
```

## 🟡 Priority 10 — Telegram Route Cache

```bash
php artisan telegram:route:cache
php artisan telegram:route:clear
```

## 🟡 Priority 11 — More Telegram Update Types

- [ ] Inline Query
- [ ] Edited Message
- [ ] Channel Post
- [ ] Edited Channel Post
- [ ] Chat Member
- [ ] My Chat Member
- [ ] Chat Join Request
- [ ] Other future Telegram update types

## 🟡 Priority 12 — Better Callback Query Routing

- [ ] Exact callback data matching
- [ ] Regular expression callback matching
- [ ] Callback route parameters
- [ ] Named callback routes
- [ ] Better inline keyboard integration

## 🟡 Priority 13 — User and Chat Conditions

- [ ] Admin-only routes
- [ ] User conditions
- [ ] Private-chat conditions
- [ ] Group-chat conditions
- [ ] Channel conditions
- [ ] User permission checks
- [ ] Chat type constraints

## 🟡 Priority 14 — Testing Tools

- [ ] Telegram fake
- [ ] Fake Telegram updates
- [ ] Route dispatch assertions
- [ ] Message sending assertions
- [ ] Callback assertions
- [ ] Conversation/state tests

# Design Principles

1. Keep Telegram routes separate from Laravel HTTP routes.
2. Use Laravel's Service Container for controller and middleware resolution.
3. Keep the public routing API simple.
4. Keep route registration separate from update processing.
5. Support Webhook and Polling through the same routing layer.
6. Follow PSR-4 autoloading.
7. Keep code clean and maintainable.
8. Keep Telegram-specific routing logic out of `routes/web.php`.
9. Build advanced features on top of the core router.

# Current Public API

```php
BOT::onCommand($command, $callback);
BOT::onText($pattern, $callback);
BOT::onCallbackQuery($callback);
BOT::fallback($callback);
BOT::onInvalid($callback);
BOT::middleware($middleware);
BOT::globalMiddleware($middleware);
BOT::group($middleware, $routes);
BOT::conversation($name);
```

Controller callbacks use:

```php
[ControllerClass::class, 'method']
```

Conversation steps use the same controller callback format or a Closure:

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->startOnCommand('register');
```
