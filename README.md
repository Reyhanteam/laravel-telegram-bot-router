# Laravel Telegram Bot Router

## Version 1.3.2

A Laravel package for routing Telegram bot updates with a Laravel-style routing system. Telegram routes live in `routes/bot.php` and are kept separate from Laravel HTTP routes.

```text
routes/web.php  -> Laravel HTTP routes
routes/bot.php  -> Telegram bot routes
```

## Current Features

- ✅ Webhook support
- ✅ Polling support
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
- ✅ Command arguments
- ✅ Route parameters
- ✅ Middleware pipeline
- ✅ Route middleware
- ✅ Global Telegram middleware
- ✅ Laravel Container middleware resolution
- ✅ Middleware objects
- ✅ Middleware short-circuit
- ✅ Middleware execution order
- ✅ Optional `TelegramMiddlewareInterface`
- ✅ Middleware groups
- ✅ Nested middleware groups
- ✅ Middleware parameters
- ✅ Named middleware aliases
- ✅ Middleware configuration
- ✅ Per-user conversation state
- ✅ Conversation steps
- ✅ Wait for next message
- ✅ Save current step
- ✅ Move to next step
- ✅ Finish conversation
- ✅ Conversation timeout
- ✅ Conversation data
- ✅ Laravel Cache storage
- ✅ Controller and Closure conversation steps
- ✅ Cancel conversation command/API
- ✅ Input validation helpers
- ✅ Explicit conversation middleware
- ✅ Conversation events
- ✅ Storage driver selection with `cacheStore()`

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

# Priority 1 — Core Telegram Routing

The core routing layer is complete and supports both Telegram delivery modes through the same routing system.

## Webhook

The package can register a Laravel endpoint for Telegram webhook updates.

```text
POST /telegram/webhook
```

Register the route with:

```bash
php artisan reyhan:setWebhookRoute
```

## Polling

Polling continuously requests Telegram updates and sends them through the same router:

```bash
php artisan reyhan:start-polling
```

## `routes/bot.php`

Telegram routes are separated from normal Laravel HTTP routes.

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;
```

Example:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

## Commands

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

## Text Routes

```php
BOT::onText('hello', [MessageController::class, 'handle']);
```

Regular expressions are supported:

```php
BOT::onText('/^hello/i', [MessageController::class, 'handle']);
```

Regex captures are available through `$update->matches`.

## Callback Queries

```php
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

Read callback data with:

```php
$update->callbackQueryData();
```

## Fallback and Invalid Updates

`fallback()` handles an update when no normal route matches.

```php
BOT::fallback(function ($update) {
    // Handle an unmatched update.
});
```

`onInvalid()` handles updates that cannot be processed as valid Telegram updates.

```php
BOT::onInvalid(function ($update) {
    // Handle an invalid update.
});
```

## Closure Handlers

Routes can execute a Closure directly:

```php
BOT::onCommand('hello', function ($update) {
    // Handle /hello
});
```

## Controller + Method Handlers

Routes can point to a controller method:

```php
BOT::onCommand('start', [StartController::class, 'index']);
```

Laravel resolves the controller through the Service Container.

## Dependency Injection

Controller dependencies can be injected normally:

```php
public function index(MyService $service, $update, array $arguments)
{
    // $service is resolved by Laravel.
}
```

Route parameters are also passed by name:

```php
public function show(MyService $service, $update, $id)
{
    // $id contains the matched route parameter.
}
```

## Improved Route Matching

The router evaluates registered routes and selects the most specific matching route.

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

A Closure can receive positional arguments:

```php
BOT::onCommand('start', function ($update, ...$arguments) {
    $name = $arguments[0] ?? null;
});
```

Command arguments are positional. Named values can instead use Route Parameters.

## Route Parameters

Route Parameters define named placeholders with `{name}` syntax.

```php
BOT::onCommand('user {id}', [UserController::class, 'show']);
```

Telegram message:

```text
/user 123
```

The router captures:

```php
[
    'id' => '123',
]
```

Access the value through `TelegramUpdate`:

```php
$id = $update->routeParameter('id');
```

Or read all parameters:

```php
$parameters = $update->routeParameters();
```

Controller methods can receive named parameters:

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

the parameters are:

```php
[
    'id' => '123',
    'section' => 'profile',
]
```

### Text Parameters

Parameters also work with text routes:

```php
BOT::onText('hello {name}', [MessageController::class, 'hello']);
```

For:

```text
hello Hossein
```

`name` is captured as `Hossein`.

Parameters match one non-whitespace segment. Use a regular expression route for more advanced matching.

## Route Constraints

Constraints validate named captures from Route Parameters and supported regex routes.

### Number

```php
BOT::onCommand('user {id}', [UserController::class, 'show'])
    ->whereNumber('id');
```

Therefore:

```text
/user 123  -> matched
/user 4567 -> matched
/user abc  -> not matched
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

If a constraint fails, the route is treated as not matched. The router can then try another route or the fallback.

## `TelegramUpdate`

The `TelegramUpdate` wrapper provides convenient access to common Telegram data:

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

# Priority 2 — Middleware

Telegram middleware follows a Laravel-style middleware pipeline.

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
        if ($update->chatId() !== self::ADMIN_CHAT_ID) {
            return null;
        }

        return $next($update);
    }
}
```

If the check succeeds, call `$next($update)`. To short-circuit the route, do not call `$next()`.

## Route Middleware

Middleware can be attached to a route:

```php
BOT::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

## Global Telegram Middleware

Global middleware runs for Telegram routes before route-specific middleware:

```php
BOT::globalMiddleware([
    LogTelegramUpdate::class,
    CheckBotState::class,
]);
```

## Middleware Objects

Middleware can be provided as class names and resolved by Laravel's Service Container. This allows middleware dependencies to be injected through the constructor.

```php
class CheckUser
{
    public function __construct(UserService $users)
    {
        // Laravel resolves UserService.
    }

    public function handle($update, \Closure $next)
    {
        return $next($update);
    }
}
```

## Middleware Short-Circuit

A middleware can stop route execution by not calling `$next()`:

```php
public function handle($update, Closure $next)
{
    if (!$this->allowed($update)) {
        return null;
    }

    return $next($update);
}
```

## Middleware Execution Order

The order is:

```text
Global middleware
        ↓
Outer group middleware
        ↓
Inner group middleware
        ↓
Route middleware
        ↓
Handler
```

The return path unwinds in the reverse order.

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

Nested groups are supported:

```php
BOT::group([AdminMiddleware::class], function () {
    BOT::group([PermissionMiddleware::class], function () {
        BOT::onCommand('settings', [SettingsController::class, 'index']);
    });
});
```

## Middleware Parameters

Middleware parameters can be supplied after a colon:

```php
BOT::middleware([
    HasPermission::class . ':users.create',
])->onCommand('create-user', [UserController::class, 'create']);
```

The middleware receives parameters after `$next`:

```php
public function handle($update, Closure $next, ...$parameters)
{
    $permission = $parameters[0] ?? null;

    return $next($update);
}
```

## Named Middleware Aliases

Register a short name:

```php
BOT::aliasMiddleware('admin', IsAdmin::class);
```

Use it on a route:

```php
BOT::middleware([
    'admin',
])->onCommand('admin', [AdminController::class, 'index']);
```

Register multiple aliases:

```php
BOT::aliasMiddlewares([
    'admin' => IsAdmin::class,
    'auth' => CheckUser::class,
    'permission' => HasPermission::class,
]);
```

Parameters work with aliases:

```php
BOT::aliasMiddleware('permission', HasPermission::class);

BOT::middleware([
    'permission:users.create',
])->onCommand('create-user', [UserController::class, 'create']);
```

Aliases are resolved through Laravel's Service Container.

## Middleware Configuration

Aliases can also be defined in `config/telegram-bot-router.php`:

```php
'middleware' => [
    'aliases' => [
        'admin' => App\Telegram\Middleware\IsAdmin::class,
        'auth' => App\Telegram\Middleware\CheckUser::class,
    ],
],
```

Then use the alias in `routes/bot.php`:

```php
BOT::middleware([
    'admin',
])->onCommand('admin', [AdminController::class, 'index']);
```

Configuration aliases are loaded by the package Service Provider.

## Optional `TelegramMiddlewareInterface`

Middleware may implement the optional package contract:

```php
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareInterface;

class AdminMiddleware implements TelegramMiddlewareInterface
{
    public function handle($update, \Closure $next)
    {
        return $next($update);
    }
}
```

A compatible middleware class can also work without implementing the interface.

# Priority 3 — Conversation / State

Conversations let a Telegram bot keep a per-user or per-chat state and wait for the next message.

Example flow:

```text
/register
   ↓
Step 1: name
   ↓
wait for next message
   ↓
Step 2: phone
   ↓
wait for next message
   ↓
Finish
```

## Start a Conversation

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->step([RegisterController::class, 'finish'])
    ->startOnCommand('register');
```

The first matching command starts the Conversation. Later messages are handled by the active Conversation step.

## Closure and Controller Steps

A Conversation can mix Closure and Controller handlers:

```php
BOT::conversation('register')
    ->step(function ($update, $input) {
        // Closure step.
    })
    ->step([RegisterController::class, 'phone'])
    ->startOnCommand('register');
```

Controller steps are resolved through Laravel's Service Container.

## Per-user Conversation State

Conversation state is stored separately using the Telegram chat/user identity, so different users can have independent active Conversations.

## Save and Move Between Steps

Each successful step advances the Conversation to the next step.

A step can return data for later steps:

```php
return [
    'data' => [
        'name' => $update->text(),
    ],
];
```

The next step can use the stored data:

```php
public function phone($update, array $data)
{
    $name = $data['name'] ?? null;
}
```

## Finish a Conversation

A step can finish the Conversation:

```php
return [
    'done' => true,
];
```

After completion, the Conversation state is cleared.

## Conversation Timeout

Set a default timeout through configuration/environment:

```env
TELEGRAM_CONVERSATION_TTL=3600
```

Or set a timeout for one Conversation:

```php
BOT::conversation('register')
    ->ttl(1800)
    ->step([RegisterController::class, 'name'])
    ->startOnCommand('register');
```

The state expires after the configured TTL when the selected cache store supports expiration.

## Conversation Data

Conversation data can be returned from a step and passed to later steps:

```php
return [
    'data' => [
        'name' => $update->text(),
    ],
];
```

## Laravel Cache Storage

Conversation state uses Laravel Cache. The package can use the Laravel cache infrastructure instead of requiring a custom storage system.

## Storage Driver Controls

A Conversation can select a Laravel cache store explicitly:

```php
BOT::conversation('register')
    ->cacheStore('database')
    ->step([RegisterController::class, 'name'])
    ->startOnCommand('register');
```

This uses the Laravel `database` cache store for the Conversation state.

## Cancel Conversation

An active Conversation can be cancelled through the package Conversation cancellation API or the configured cancellation command.

Example command usage:

```text
/cancel
```

Cancellation clears the active Conversation state so the next message is handled normally.

## Input Validation Helpers

Conversation input provides validation helpers for common checks.

```php
BOT::conversation('validation')
    ->step(function ($update, $input) {
        $value = $input
            ->required()
            ->string()
            ->minLength(3)
            ->maxLength(20)
            ->value();

        // Use $value after validation succeeds.
    })
    ->startOnCommand('validate');
```

If validation fails, the current Conversation step remains active. The next message can be validated again.

## Explicit Conversation Middleware

Middleware can be attached directly to a Conversation:

```php
BOT::conversation('admin-flow')
    ->middleware([
        AdminMiddleware::class,
    ])
    ->step(function ($update, $input) {
        // Only runs when the Conversation middleware allows it.
    })
    ->startOnCommand('adminflow');
```

Middleware is resolved through Laravel's container and can short-circuit the Conversation.

## Conversation Events

The package dispatches Conversation lifecycle events.

Available lifecycle events include:

```php
ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationStarted
ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationStepCompleted
ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationFinished
```

Laravel listeners can subscribe to these events.

Example:

```php
use Illuminate\Support\Facades\Event;
use ReyhanTeam\TelegramBotRouter\Conversation\Events\ConversationStarted;

Event::listen(ConversationStarted::class, function ($event) {
    // Handle Conversation start.
});
```

# Webhook and Polling

Webhook and Polling use the same Telegram routing layer. This keeps route definitions independent of the Telegram update transport method.

## Webhook

```bash
php artisan reyhan:setWebhookRoute
```

Default endpoint:

```text
POST /telegram/webhook
```

## Polling

```bash
php artisan reyhan:start-polling
```

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

# Roadmap / Feature Status

## Priority 1 — Core Telegram Routing

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

## Priority 2 — Middleware ⭐

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
- [x] Named middleware aliases
- [x] Middleware configuration

## Priority 3 — Conversation / State ⭐⭐⭐

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
- [x] Cancel conversation command/API
- [x] Input validation helpers
- [x] Explicit conversation middleware
- [x] Conversation events
- [x] More storage driver controls

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

## Priority 6 — Rate Limiting

Rate limiting uses Laravel's Cache-backed RateLimiter. It can limit Telegram traffic by user, chat, and command.

Enable the built-in limits in `config/telegram-bot-router.php`:

```php
'rate_limit' => [
    'enabled' => true,
    'limits' => [
        'user' => ['max_attempts' => 60, 'decay_seconds' => 60],
        'chat' => ['max_attempts' => 120, 'decay_seconds' => 60],
        'command' => ['max_attempts' => 30, 'decay_seconds' => 60],
    ],
],
```

Environment variables are also supported:

```env
TELEGRAM_RATE_LIMIT_ENABLED=true
TELEGRAM_RATE_LIMIT_USER_MAX=60
TELEGRAM_RATE_LIMIT_USER_DECAY=60
TELEGRAM_RATE_LIMIT_CHAT_MAX=120
TELEGRAM_RATE_LIMIT_CHAT_DECAY=60
TELEGRAM_RATE_LIMIT_COMMAND_MAX=30
TELEGRAM_RATE_LIMIT_COMMAND_DECAY=60
```

Route-specific limits override the global configuration:

```php
BOT::onCommand('start', [StartController::class, 'index'])
    ->rateLimit('user', 5, 60)
    ->rateLimit('command', 2, 60);
```

Multiple limits can be configured at once:

```php
BOT::onCommand('profile', [ProfileController::class, 'show'])
    ->rateLimits([
        'user' => ['max_attempts' => 10, 'decay_seconds' => 60],
        'chat' => ['max_attempts' => 30, 'decay_seconds' => 60],
        'command' => ['max_attempts' => 5, 'decay_seconds' => 60],
    ]);
```

When a limit is exceeded, the route handler is not executed. Counters use Laravel's configured cache infrastructure.

## 🟠 Priority 6 — Rate Limiting

- [x] Per-user limits
- [x] Per-chat limits
- [x] Per-command limits
- [x] Configurable limits
- [x] Laravel Cache integration

## 🟠 Priority 7 — Telegram Route List

```bash
php artisan telegram:route:list
```

## 🟠 Priority 8 — Queue Support

The package uses Laravel's native queue system. This lets the webhook return
quickly while slower controller work and outgoing messages run in a queue
worker.

### Features

- [x] Queue update processing
- [x] Queue message sending
- [x] Queue heavy bot tasks
- [x] Laravel queue integration
- [x] Queue retry handling
- [x] Configurable retry attempts
- [x] Retry delay / backoff
- [x] Failed Telegram jobs
- [x] Failed job handling
- [x] Failed job events
- [x] Queue exception handling
- [x] Job timeout configuration
- [x] Job middleware support
- [x] Prevent duplicate update processing
- [x] Queue logging
- [x] Queue configuration

### 1. Configure Laravel Queue

First configure a Laravel queue connection as usual. For a local development
project, Laravel's database queue is a common option:

```bash
php artisan queue:table
php artisan migrate
```

Then put the following values in the application's `.env` file. These are the
package Queue settings:

```env
TELEGRAM_QUEUE_CONNECTION=
TELEGRAM_QUEUE_NAME=default
TELEGRAM_QUEUE_TRIES=3
TELEGRAM_QUEUE_BACKOFF=10,30,60
TELEGRAM_QUEUE_TIMEOUT=120
TELEGRAM_QUEUE_DEDUPLICATE_UPDATES=true
TELEGRAM_QUEUE_DEDUPLICATION_TTL=86400
TELEGRAM_QUEUE_CACHE_STORE=
```

`TELEGRAM_QUEUE_CONNECTION` is optional. When it is empty, Laravel uses the
application's default queue connection. Set it, for example, to `database`,
`redis`, or another connection configured in `config/queue.php`.

`TELEGRAM_QUEUE_NAME` is the queue listened to by the worker. Run a worker for
that same queue:

```bash
php artisan queue:work --queue=default
```

`TELEGRAM_QUEUE_TRIES=3` means Laravel may attempt a failed Telegram job up to
three times. `TELEGRAM_QUEUE_BACKOFF=10,30,60` delays those retries by 10, 30,
and 60 seconds. `TELEGRAM_QUEUE_TIMEOUT=120` gives each attempt 120 seconds to
finish.

`TELEGRAM_QUEUE_DEDUPLICATE_UPDATES=true` protects a bot from processing the
same Telegram `update_id` twice. The successful update lock is retained for
`TELEGRAM_QUEUE_DEDUPLICATION_TTL` seconds. Use a shared cache store such as
Redis when multiple queue workers or servers are running. Set
`TELEGRAM_QUEUE_CACHE_STORE` to the name of that Laravel cache store, or leave
it empty to use the default store.

### 2. Queue Telegram Route Handlers

Queue one route by adding `->queue()` after its handler:

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;

BOT::onCommand('report', [ReportController::class, 'generate'])
    ->queue();
```

To use a separate queue for a heavy task, provide the queue name:

```php
BOT::onCommand('export', [ExportController::class, 'create'])
    ->queue('telegram-heavy');
```

Queued route handlers must be controller actions or another serializable
action. Closures cannot be queued by Laravel and must not be used with
`->queue()`.

To queue every Telegram route by default, add this to `.env`:

```env
TELEGRAM_QUEUE_UPDATES=true
```

Route middleware and rate limits still run before the update is placed on the
queue. The controller action is executed by the queue worker.

### 3. Queue Outgoing Messages

Use `TelegramQueue::sendMessage()` when sending a message does not need to
block the current request:

```php
use ReyhanTeam\TelegramBotRouter\TelegramQueue;

TelegramQueue::sendMessage([
    'chat_id' => 123456789,
    'text' => 'Your report is ready.',
]);
```

The message job uses this package's `TelegramApiClient`, so it uses the same
bot token and Telegram API configuration as the rest of the package.

### 4. Handle Failed Telegram Jobs

When an attempt throws an exception, it is logged and Laravel retries it using
the configured attempts and backoff. Once all attempts are exhausted, Laravel
runs the job's failure handler, records it through the configured Laravel
failed-job driver, logs the failure, and dispatches `TelegramJobFailed`.

Listen for the event in a Laravel service provider to notify your team or save
additional diagnostics:

```php
use Illuminate\Support\Facades\Event;
use ReyhanTeam\TelegramBotRouter\Events\TelegramJobFailed;

Event::listen(TelegramJobFailed::class, function (TelegramJobFailed $event) {
    // $event->job
    // $event->context
    // $event->exception
    // $event->attempts
});
```

Use Laravel's normal failed-job commands to inspect or retry jobs:

```bash
php artisan queue:failed
php artisan queue:retry all
```

### 5. Queue Job Middleware

All package queue jobs can use Laravel queue middleware. Add middleware class
names or middleware objects to `queue.middleware` in the published
`config/telegram-bot-router.php` file:

```php
'queue' => [
    // Other Queue settings...
    'middleware' => [
        App\Jobs\Middleware\ThrottleTelegram::class,
    ],
],
```

The middleware is resolved through Laravel's service container for each job.

### Queue Lifecycle

```text
Telegram update
    ↓
Route middleware and rate limits
    ↓
Queue job is dispatched
    ↓
Duplicate update check (update_id + cache)
    ↓
Controller action or Telegram API message
    ↓
Success log

On exception: log → backoff → retry
After final failure: Laravel failed job → TelegramJobFailed event
```

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


## Status

Priority 1 through Priority 6 features are implemented and tested in the current development cycle.
