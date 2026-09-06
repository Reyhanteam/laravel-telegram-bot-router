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

### Webhook Security

For production, configure Telegram's webhook secret token:

```env
TELEGRAM_WEBHOOK_SECRET_TOKEN=replace-with-a-random-secret
```

When this value is configured, the package requires Telegram's:

```text
X-Telegram-Bot-Api-Secret-Token
```

HTTP header on every webhook request. The comparison uses a constant-time `hash_equals()` check. Missing or incorrect secrets are rejected with HTTP `401` before the update is parsed or routed.

Leaving the configuration empty disables this verification for backwards compatibility. Production deployments should always configure a secret and keep it out of source control.

For the complete security policy, threat model, secret-rotation procedure, and production hardening checklist, see [`SECURITY.md`](SECURITY.md).

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
