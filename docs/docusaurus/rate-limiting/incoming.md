---
title: Incoming Rate Limits
---
# Incoming Rate Limits

Incoming Telegram traffic can be rate limited through package configuration and route-level rate-limit settings.

```php
Route::onCommand('start', [StartController::class, 'index'])
    ->rateLimit('user', 5, 60);
```
