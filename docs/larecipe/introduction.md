# Laravel Telegram Bot Router

> **A Laravel-native routing and management system for Telegram bots.**
>
> Build Telegram bots with the same clean structure you already use in Laravel.

---

## Overview

**Laravel Telegram Bot Router** is a Laravel-native package for building and managing Telegram bots with a routing architecture that feels familiar to Laravel developers.

Instead of mixing Telegram bot logic with Laravel HTTP routes, the package gives your bot its own routing layer:

```text
routes/web.php
    ↓
Laravel HTTP application

routes/bot.php
    ↓
Telegram Bot application
```

This separation keeps Telegram-specific behavior outside your normal web routes and gives your bot application a clear, maintainable structure.

---

## The idea behind the package

A Telegram bot receives **updates** from Telegram. An update can contain a command, a text message, a callback query, and other Telegram events.

The package receives that update and sends it through the same kind of routing flow that Laravel developers already understand:

```text
Telegram
   │
   │ Update
   ▼
Telegram Bot Router
   │
   ├── Match route
   ├── Run middleware
   ├── Resolve controller
   └── Execute handler
            │
            ▼
       Your application
```

For example, you can define:

```php
use ReyhanTeam\TelegramBotRouter\Facades\Route;

Route::onCommand('start', [StartController::class, 'index']);
```

When a user sends:

```text
/start
```

the router finds the matching Telegram route and calls:

```php
StartController::index()
```

Your bot logic stays in your application controllers instead of becoming one large webhook handler.

---

## Why a separate bot router?

A Telegram bot is an application with its own input types, middleware, commands, conversations, callbacks, permissions, queues, and state.

Putting all of that logic inside `routes/web.php` or inside one webhook controller can quickly become difficult to maintain.

This package provides a dedicated place for Telegram routes:

```text
Laravel application
│
├── routes/
│   ├── web.php       → HTTP routes
│   └── bot.php       → Telegram routes
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── ...
│
└── config/
    └── telegram-bot-router.php
```

The result is a cleaner separation between the **web application** and the **Telegram bot application**.

---

## Laravel-style routing

The router supports more than simple commands.

### Commands

```php
Route::onCommand('start', [StartController::class, 'index']);
```

### Text messages

```php
Route::onText('hello', [MessageController::class, 'hello']);
```

### Callback queries

```php
Route::onCallbackQuery([ProfileController::class, 'show']);
```

### Route parameters

```php
Route::onCommand(
    'user {id}',
    [UserController::class, 'show']
)->whereNumber('id');
```

The same routing layer can then provide the matched parameters to your handler.

---

## Controllers instead of a monolithic webhook

A typical Telegram bot can contain many behaviors:

- `/start`
- `/help`
- `/profile`
- Text messages
- Inline keyboard callbacks
- Conversations
- Admin commands
- Group events
- Queue jobs
- API responses

The package lets you map these behaviors to dedicated controllers and methods.

```php
Route::onCommand('start', [StartController::class, 'index']);
Route::onCommand('help', [HelpController::class, 'index']);
Route::onCommand('profile', [ProfileController::class, 'show']);
```

This keeps each part of the bot focused on one responsibility.

Laravel's Service Container is also used to resolve controller dependencies.

---

## Webhook and polling

The package supports two common ways to receive Telegram updates.

### Webhook

Telegram sends updates to your Laravel application through an HTTP endpoint.

The default endpoint is:

```text
POST /telegram/webhook
```

The webhook request is processed by the package and then passed to the Telegram routing layer.

For production deployments, the package also supports Telegram's webhook secret-token verification.

```text
Telegram
   │
   │ POST /telegram/webhook
   ▼
Laravel
   │
   ▼
Webhook verification
   │
   ▼
Telegram Router
```

### Polling

For development or environments where a public webhook is not available, the package can use polling:

```bash
php artisan reyhan:start-polling
```

The polling process retrieves Telegram updates and sends them through the **same routing layer** used by webhook mode.

This means your routes do not need to change when you change the transport method.

---

## One routing layer, two transports

A key design principle of the package is that **webhook and polling are transport mechanisms, not different routing systems**.

```text
             Telegram
                │
        ┌───────┴───────┐
        │               │
     Webhook         Polling
        │               │
        └───────┬───────┘
                ▼
        Telegram Router
                │
        ┌───────┴────────┐
        │                │
    Middleware       Route Match
                         │
                         ▼
                  Controller / Closure
```

Your `routes/bot.php` definitions remain the same.

---

## Middleware and conditions

Telegram routes can use middleware just like other Laravel application routes.

```php
Route::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand(
    'admin',
    [AdminController::class, 'index']
);
```

The package also supports route and application conditions such as user, chat, admin, permission, and chat-type checks.

This allows authentication and authorization rules to remain close to the route they protect.

---

## Conversations and state

Some Telegram interactions are not a single request-response operation.

For example:

```text
/start
   ↓
Ask for name
   ↓
Receive name
   ↓
Ask for phone
   ↓
Receive phone
   ↓
Finish registration
```

The package provides a conversation and state layer for these multi-step interactions.

```php
Route::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->startOnCommand('register');
```

Conversation state can use Laravel Cache and can be configured with a specific cache store.

---

## Queue support

Telegram updates can also be processed through Laravel Queue.

This is useful when a bot performs work that should not block the incoming update path.

```text
Telegram Update
      ↓
Laravel Queue
      ↓
Telegram Queue Job
      ↓
Router
      ↓
Middleware
      ↓
Controller
```

Queue processing supports configurable:

- Connection
- Queue name
- Attempts
- Backoff
- Timeout
- Deduplication
- Retryable and non-retryable exception behavior

Failed jobs use Laravel's `failed_jobs` storage as the canonical persistence layer, and the package emits a `TelegramJobFailed` event for failure handling and inspection.

---

## Telegram API

The package includes a developer-friendly Telegram API layer.

This keeps outgoing Telegram operations inside the package ecosystem instead of requiring application code to depend on a separate Telegram SDK.

The API layer also supports fake responses for package tests, so Telegram API calls can be tested without making real network requests.

---

## Keyboard builder

The package includes a keyboard builder foundation for Telegram Inline and Reply keyboards.

It supports features such as:

- Inline keyboards
- Reply keyboards
- Callback buttons
- URL buttons
- WebApp buttons
- Login buttons
- Switch-inline buttons
- Pay buttons
- Rows and chaining
- Dynamic and conditional buttons
- Keyboard factories
- Callback-data helpers
- Validation and JSON serialization

Example:

```php
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;

$keyboard = Keyboard::make()
    ->inline()
    ->row()
    ->button('Profile', 'profile')
    ->button('Help', 'help');
```

---

## Security

The package treats webhook authentication and application authorization as separate concerns.

When a webhook secret is configured, incoming requests are checked before the update is parsed and routed.

```text
Public webhook request
        ↓
Secret-token verification
        ↓
Update validation
        ↓
Telegram Router
        ↓
Middleware / authorization
        ↓
Controller
```

Configure the secret with:

```env
TELEGRAM_WEBHOOK_SECRET_TOKEN=replace-with-a-random-secret
```

The package uses constant-time comparison for the configured secret and rejects missing or invalid secrets with HTTP `401 Unauthorized`.

Webhook authentication does not replace application-level authorization. Your bot should still enforce user, chat, role, and permission rules where required.

---

## Designed for Laravel developers

The package follows Laravel concepts where they make sense:

| Laravel concept | Telegram Bot Router equivalent |
| --- | --- |
| `routes/web.php` | `routes/bot.php` |
| HTTP route | Telegram route |
| Controller | Bot controller |
| Middleware | Telegram middleware |
| Service Container | Controller dependency injection |
| Queue | Queued Telegram updates |
| Cache | Conversation state |
| Events | Telegram lifecycle events |
| Route parameters | Telegram command parameters |
| Route conditions | User / chat / permission conditions |

The goal is simple:

> **Telegram bot development should feel like Laravel development.**

---

## A small example

A minimal bot can start with only a few routes:

```php
use App\Http\Controllers\Telegram\HelpController;
use App\Http\Controllers\Telegram\StartController;
use ReyhanTeam\TelegramBotRouter\Facades\Route;

Route::onCommand('start', [StartController::class, 'index']);
Route::onCommand('help', [HelpController::class, 'index']);
Route::onText('hello', [StartController::class, 'hello']);
```

The application can then grow without turning the webhook handler into the center of the entire bot.

---

## What the package provides

At a high level, Laravel Telegram Bot Router brings these pieces together:

```text
┌──────────────────────────────────────────────┐
│          Laravel Telegram Bot Router         │
├──────────────────────────────────────────────┤
│                                              │
│  Routing             Webhook / Polling       │
│  Controllers         Middleware              │
│  Conversations      Queue                   │
│  Telegram API        Keyboard Builder        │
│  Conditions          Events                  │
│  Testing             Security                │
│                                              │
└──────────────────────────────────────────────┘
```

It is not only a Telegram API wrapper. Its main purpose is to provide a **routing and application architecture for Telegram bots inside Laravel**.

---

## Next step

Ready to build your first bot?

Start with the installation guide and then create your Telegram routes in:

```text
routes/bot.php
```

From there, you can add controllers, middleware, conversations, keyboards, queues, and Telegram API calls as your bot grows.
