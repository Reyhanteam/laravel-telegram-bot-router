---
title: Conversations
---
# Conversations

Conversations provide stateful multi-step interactions for Telegram users.

```php
Route::conversation('registration')
    ->step(fn ($update) => null)
    ->step(fn ($update) => null)
    ->startOnCommand('start');
```
