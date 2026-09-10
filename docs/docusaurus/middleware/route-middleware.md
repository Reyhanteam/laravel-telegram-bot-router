---
title: Route Middleware
---
# Route Middleware

Attach middleware to a Telegram route using `Route::middleware([...])`:

```php
Route::middleware([AuthenticateTelegramUser::class])
    ->onCommand('profile', [ProfileController::class, 'show']);
```
