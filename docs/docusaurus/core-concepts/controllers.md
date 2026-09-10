---
title: Controllers
---
# Controllers

Telegram routes can point to Laravel controller methods:

```php
Route::onCommand('start', [StartController::class, 'index']);
```

Keep route declarations focused on routing and move application logic into controllers and services.
