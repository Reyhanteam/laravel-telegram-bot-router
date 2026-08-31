# Laravel Telegram Bot Router

## Version 1.1.1

> **Status:** Webhook, Polling, Telegram Middleware, Conversation State, improved route matching, and route constraints are working in the current development version.

- ✅ Webhook
- ✅ Polling
- ✅ `routes/bot.php`
- ✅ `BOT::onCommand()`
- ✅ `BOT::onText()`
- ✅ `BOT::onCallbackQuery()`
- ✅ `BOT::fallback()`
- ✅ `BOT::onInvalid()`
- ✅ Closure handlers
- ✅ Controller + method handlers
- ✅ Laravel Service Container controller resolution
- ✅ Dependency Injection for controller methods
- ✅ Regular expression matching for text routes
- ✅ `TelegramUpdate` wrapper
- ✅ Improved route matching
- ✅ Route constraints
- ✅ Route middleware
- ✅ Global Telegram middleware
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

The command matcher reads only the command token. Extra whitespace or command arguments do not prevent the command route from matching:

```text
/start
/start hello
/start    hello
```

Command arguments are not extracted yet. Argument extraction is planned for a future route feature.

Bot usernames are ignored when matching commands:

```text
/start@MyBot
```

matches:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

### Text

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

### Fallback and Invalid Updates

```php
BOT::fallback(function ($update) {
    // Handle unmatched updates.
});

BOT::onInvalid(function ($update) {
    // Handle invalid updates.
});
```

## Improved Route Matching

The router evaluates all registered routes and selects the most specific matching route instead of always using the first matching route.

Current matching priority is:

```text
Exact command/text match
        ↓
Regular expression text/callback match
        ↓
Generic callback-query route
```

Example:

```php
BOT::onText('/^hello/i', [MessageController::class, 'generic']);
BOT::onText('hello', [MessageController::class, 'exact']);
```

For `hello`, the exact route is selected.

Route registration order remains the tie-breaker when routes have the same score.

### Command Matching Details

1. Leading and trailing whitespace is removed.
2. The command must start with `/`.
3. The command token is separated from the rest of the message.
4. A Telegram bot username after `@` is ignored.
5. The normalized command is compared with the registered command.

## Route Constraints

Route constraints allow a route to validate named values captured by a regular expression.

The constraint API is fluent:

```php
BOT::onText('/^user (?<id>[^ ]+)$/', [UserController::class, 'show'])
    ->whereNumber('id');
```

The route matches only when the named `id` capture contains digits.

For example:

```text
user 123       -> match
user 4567      -> match
user abc       -> no match
```

### Custom Constraint

Use `where()` for a custom regular expression:

```php
BOT::onText('/^user (?<id>[^ ]+)$/', [UserController::class, 'show'])
    ->where('id', '[0-9]{4}');
```

This matches exactly four digits.

### Built-in Constraints

#### Number

```php
->whereNumber('id')
```

Uses:

```text
\d+
```

#### Alpha

```php
->whereAlpha('name')
```

Allows ASCII letters:

```text
[A-Za-z]+
```

#### Alpha Numeric

```php
->whereAlphaNumeric('username')
```

Allows ASCII letters and numbers:

```text
[A-Za-z0-9]+
```

#### Allowed Values

```php
->whereIn('section', ['profile', 'settings'])
```

Only the listed values are accepted.

### Multiple Constraints

Constraints can be chained:

```php
BOT::onText(
    '/^user (?<id>[^ ]+) (?<section>[^ ]+)$/',
    [UserController::class, 'show']
)
    ->whereNumber('id')
    ->whereIn('section', ['profile', 'settings']);
```

A route must satisfy all constraints.

### How Constraints Work

The regular expression first captures named values:

```text
/^user (?<id>[^ ]+)$/
```

Then the router checks each declared constraint against the corresponding named capture.

If a named capture is missing, or its value does not satisfy the constraint, the route is treated as **not matched**. The router can then try another route or use the fallback.

The captured values remain available through:

```php
$update->matches['id']
```

> **Current limitation:** Constraints currently validate named captures from regular-expression routes. Placeholder syntax such as `{id}` and automatic controller parameter injection are part of the planned Route Parameters feature.

## Controllers

Controller actions use Laravel's Service Container:

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

A common location is:

```text
app/Telegram/Middleware/
```

Example:

```text
app/Telegram/Middleware/AdminMiddleware.php
```

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

`$update` is the current `TelegramUpdate`. `$next` continues the pipeline.

If access is allowed:

```php
return $next($update);
```

If access is denied, do not call `$next()`:

```php
return null;
```

This stops the pipeline. The controller and later middleware do not run.

## Route Middleware

```php
BOT::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

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

Nested groups are supported.

## Middleware Parameters

```php
BOT::middleware([
    HasPermission::class . ':users.create',
])->onCommand('create-user', [UserController::class, 'create']);
```

A middleware receives parameters after `$next` through `...$parameters`.

## Middleware Contract

A middleware can optionally implement:

```php
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareInterface;
```

The contract is optional when the class provides a compatible `handle()` method.

# Conversation / State

Conversation State lets a bot wait for the user's next message.

Example:

```text
Bot: Please enter your name.
User: Hossein
Bot: Please enter your phone number.
User: 0912...
```

The package stores active conversation state separately for each Telegram chat and user.

## Starting a Conversation

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->step([RegisterController::class, 'finish'])
    ->startOnCommand('register');
```

Flow:

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

## Conversation Data

A step can return updated data:

```php
return [
    'data' => [
        'name' => $update->text(),
    ],
];
```

The next step receives the data as its second argument.

## Completion

```php
return [
    'done' => true,
];
```

The conversation state is then removed.

## Timeout

```env
TELEGRAM_CONVERSATION_TTL=3600
```

Or per conversation:

```php
->ttl(1800)
```

Conversation state uses Laravel Cache.

# Webhook

Default endpoint:

```text
POST /telegram/webhook
```

Register it with:

```bash
php artisan reyhan:setWebhookRoute
```

# Polling

```bash
php artisan reyhan:start-polling
```

Polling uses the same routing layer as Webhook mode.

# Feature Roadmap

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
- [x] Better route matching
- [x] Route constraints

Planned:

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

Planned:

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
                     Route Constraints
                            │
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

Route constraints return a fluent registrar:

```php
BOT::onText('/^user (?<id>[^ ]+)$/', [UserController::class, 'show'])
    ->whereNumber('id');
```

Supported constraint methods:

```php
->where($name, $expression)
->whereNumber($name)
->whereAlpha($name)
->whereAlphaNumeric($name)
->whereIn($name, $values)
```

Controller callbacks use:

```php
[ControllerClass::class, 'method']
```
