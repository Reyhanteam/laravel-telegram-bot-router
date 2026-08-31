# Laravel Telegram Bot Router

## Version 1.1.1

A Laravel package for routing Telegram bot updates with a Laravel-style routing system. Telegram routes live in `routes/bot.php` and are kept separate from Laravel HTTP routes.

```text
routes/web.php  -> Laravel HTTP routes
routes/bot.php  -> Telegram bot routes
```

## Current Features

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
- ✅ Laravel Service Container resolution
- ✅ Controller Dependency Injection
- ✅ Regular expression text matching
- ✅ `TelegramUpdate` wrapper
- ✅ Improved route matching
- ✅ Route constraints
- ✅ Command arguments
- ✅ Route parameters
- ✅ Route middleware
- ✅ Global middleware
- ✅ Middleware groups and nested groups
- ✅ Middleware parameters
- ✅ Middleware short-circuit
- ✅ Conversation state
- ✅ Per-user conversation storage
- ✅ Conversation steps
- ✅ Wait for next message
- ✅ Conversation timeout
- ✅ Conversation data

## Version 1.1.4

### What's included in v1.1.4

- ✅ Better Route Matching
- ✅ Route Constraints
- ✅ Command Arguments
- ✅ Route Parameters
- ✅ Route Parameter access through `TelegramUpdate`
- ✅ Multiple Route Parameters
- ✅ Route Parameters with Constraints
- ✅ Command Arguments are passed to Closure handlers as an array

**Priority 1 — Core Telegram Routing is now complete.**


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

Use `BOT` in `routes/bot.php`:

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;
```

### Commands

```php
BOT::onCommand('start', [StartController::class, 'index']);
BOT::onCommand('/help', [HelpController::class, 'index']);
```

The leading `/` is optional when registering a command.

Telegram bot usernames are ignored during matching:

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

Regex captures are available through `$update->matches`.

### Callback Query

```php
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

Read callback data with:

```php
$update->callbackQueryData();
```

### Fallback and Invalid Updates

```php
BOT::fallback(function ($update) {
    // Handle an unmatched update.
});

BOT::onInvalid(function ($update) {
    // Handle an invalid update.
});
```

## Improved Route Matching

The router evaluates registered routes and selects the most specific matching route instead of always using the first match.

Current priority:

```text
Exact command/text match
        ↓
Route parameter match
        ↓
Regular expression match
        ↓
Generic callback-query route
```

Example:

```php
BOT::onText('/^hello/i', [MessageController::class, 'generic']);
BOT::onText('hello', [MessageController::class, 'exact']);
BOT::onText('hello {name}', [MessageController::class, 'named']);
```

For `hello`, the exact route wins.

For `hello Hossein`, the named-parameter route can match.

When routes have the same score, registration order is used as the tie-breaker.

## Command Arguments

Text after a command is available as positional arguments.

```text
/start Hossein
```

Route:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

Controller:

```php
public function index($update, array $arguments)
{
    $name = $arguments[0] ?? null;
}
```

The same arguments are available from `TelegramUpdate`:

```php
$arguments = $update->commandArguments();
```

For:

```text
/start one two three
```

the result is:

```php
[
    'one',
    'two',
    'three',
]
```

Multiple spaces are normalized.

A Closure can receive the positional arguments:

```php
BOT::onCommand('start', function ($update, ...$arguments) {
    $name = $arguments[0] ?? null;
});
```

Command arguments are positional. Named values can instead use Route Parameters.

## Route Parameters

Route Parameters let a route define named placeholders with `{name}` syntax.

### Command Parameters

```php
BOT::onCommand('user {id}', [UserController::class, 'show']);
```

Telegram message:

```text
/user 123
```

The router matches the route and captures:

```php
[
    'id' => '123',
]
```

The value is available through `TelegramUpdate`:

```php
$id = $update->routeParameter('id');
```

Or all parameters:

```php
$parameters = $update->routeParameters();
```

Controller methods can receive named parameters through Laravel's Service Container:

```php
public function show($update, $id)
{
    // $id === '123'
}
```

### Multiple Parameters

```php
BOT::onCommand('user {id} {section}', [UserController::class, 'show']);
```

For:

```text
/user 123 profile
```

parameters are:

```php
[
    'id' => '123',
    'section' => 'profile',
]
```

### Text Parameters

Parameters can also be used with text routes:

```php
BOT::onText('hello {name}', [MessageController::class, 'hello']);
```

For:

```text
hello Hossein
```

`name` is captured as `Hossein`.

Parameters match one non-whitespace segment. Use a regular expression route when more advanced text matching is required.

### Parameters and Constraints

Route Parameters work together with Route Constraints:

```php
BOT::onCommand('user {id}', [UserController::class, 'show'])
    ->whereNumber('id');
```

Therefore:

```text
/user 123    -> matched
/user 4567   -> matched
/user abc    -> not matched
```

### Route Parameter vs Command Argument

Use Command Arguments when the command accepts general positional values:

```php
BOT::onCommand('search', [SearchController::class, 'search']);
```

Use Route Parameters when a value has a specific name and belongs to the route definition:

```php
BOT::onCommand('user {id}', [UserController::class, 'show']);
```

Command arguments:

```text
/search phone android
```

Route parameters:

```text
/user 123
```

A route can use both mechanisms. Named route parameters are exposed separately through `routeParameters()`.

## Route Constraints

Constraints validate named captures from regular-expression routes and Route Parameters.

### Number

```php
BOT::onText('/^user (?<id>[^ ]+)$/', [UserController::class, 'show'])
    ->whereNumber('id');
```

The same constraint works with Route Parameters:

```php
BOT::onCommand('user {id}', [UserController::class, 'show'])
    ->whereNumber('id');
```

### Custom Regex

```php
->where('id', '[0-9]{4}');
```

### Alpha

```php
->whereAlpha('name');
```

### Alpha Numeric

```php
->whereAlphaNumeric('username');
```

### Allowed Values

```php
->whereIn('section', ['profile', 'settings']);
```

Multiple constraints can be chained:

```php
BOT::onCommand('user {id} {section}', [UserController::class, 'show'])
    ->whereNumber('id')
    ->whereIn('section', ['profile', 'settings']);
```

If a constraint fails, the route is treated as not matched and the router can try another route or the fallback.

## Controllers and Dependency Injection

Controller actions are resolved through Laravel's Service Container:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

Dependencies can be injected normally:

```php
public function index(MyService $service, $update, array $arguments)
{
    // ...
}
```

Route parameters are also passed by name:

```php
public function show(MyService $service, $update, $id)
{
    // ...
}
```

## TelegramUpdate

Useful methods:

```php
$update->chatId();
$update->userId();
$update->messageId();
$update->text();
$update->callbackQueryData();
$update->commandArguments();
$update->routeParameters();
$update->routeParameter('id');
$update->originalUpdate();
```

Nested Telegram data is also available:

```php
$update->message->chat->id;
$update->message->from->id;
$update->message->text;
```

# Middleware

Telegram middleware follows the Laravel middleware pipeline model.

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

## Creating Middleware

A common application location is:

```text
app/Telegram/Middleware/
```

Example:

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

If the check succeeds, call `$next($update)` to continue. If it fails, do not call `$next()`.

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

## Groups

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

Parameters are available after `$next` through `...$parameters`.

## Middleware Contract

A middleware may implement:

```php
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareInterface;
```

The contract is optional when the class has a compatible `handle()` method.

# Conversation / State

Conversation State lets the bot wait for the user's next message.

Example:

```text
/register
   ↓
Ask for name
   ↓
wait for next message
   ↓
Ask for phone
   ↓
wait for next message
   ↓
Finish
```

Start a conversation with:

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->step([RegisterController::class, 'finish'])
    ->startOnCommand('register');
```

Conversation state is stored separately for each Telegram chat/user.

### Conversation Data

A step can return data:

```php
return [
    'data' => [
        'name' => $update->text(),
    ],
];
```

The next step receives the data:

```php
public function phone($update, array $data)
{
    $name = $data['name'] ?? null;
}
```

### Finish

```php
return [
    'done' => true,
];
```

### Timeout

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

Start polling with:

```bash
php artisan reyhan:start-polling
```

Webhook and Polling use the same Telegram routing layer.

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

# Roadmap

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
- [x] Command arguments
- [x] Route parameters

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
- [ ] Named middleware aliases
- [ ] Middleware configuration

## 🔴 Priority 3 — Conversation / State ⭐⭐⭐

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
2. Use Laravel's Service Container for controllers and middleware.
3. Keep the public routing API simple.
4. Keep route registration separate from update processing.
5. Support Webhook and Polling through the same routing layer.
6. Follow PSR-4 autoloading.
7. Keep the code clean and maintainable.
8. Keep Telegram-specific routing logic out of `routes/web.php`.
9. Build advanced features on top of the core router.
