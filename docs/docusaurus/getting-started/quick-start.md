---
title: Quick Start
---
# Quick Start

Publish the package routes, create a Telegram controller, and register a command in `routes/bot.php`:

```php
Route::onCommand('start', [StartController::class, 'index']);
```

The package can receive updates through webhook or polling.
