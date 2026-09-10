---
title: Introduction
---

# Laravel Telegram Bot Router

Laravel Telegram Bot Router is a Laravel-focused package for routing Telegram bot updates through a dedicated `routes/bot.php` file.

It separates Telegram routing from Laravel's normal HTTP routes and lets you map commands, text messages, callback queries, and other Telegram updates to controller methods or callbacks.

## Core idea

```php
Route::onCommand('start', [StartController::class, 'index']);
```

Telegram delivers an update through webhook or polling, the package resolves the matching Telegram route, applies the configured middleware and conditions, and dispatches the handler.

The package is designed around Laravel conventions, PSR-4 autoloading, and clean separation of routing and application logic.

## Main areas

- Telegram routing
- Webhook and polling transports
- Controllers and middleware
- Conversations
- Inline and reply keyboards
- Telegram API helpers
- Queued update processing
- Rate limiting
- Testing and security
