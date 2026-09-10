---
title: Commands
---
# Commands

Register Telegram commands with `Route::onCommand()`.

```php
Route::onCommand('start', [StartController::class, 'index']);
Route::onCommand('help', [HelpController::class, 'index']);
```
