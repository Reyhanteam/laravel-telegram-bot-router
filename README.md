# Laravel Telegram Bot Router

## Version 1.0.8

> **Status:** Both Telegram update modes are working correctly in this version.
>
> - ✅ Webhook — Working
> - ✅ Polling — Working
>
> Tested with Laravel 13.29.0 and PHP 8.5.4.

## Project Goal

Laravel Telegram Bot Router provides a Laravel-style routing system for Telegram bots. It keeps Telegram bot routes separate from Laravel's HTTP routes and provides a clean structure for building Telegram bots with controllers, middleware, events, and other Laravel features.

Telegram bot routes are defined in `routes/bot.php`, while normal Laravel HTTP routes remain in `routes/web.php`.

The package supports both Telegram update modes:

- **Webhook:** Telegram sends updates to a Laravel webhook route, and the package processes the update.
- **Polling:** The package continuously requests updates from Telegram in a polling loop and processes them through the Telegram router.

## Feature Roadmap

The following roadmap lists the planned and recommended features in priority order. Higher-priority items should be implemented first because they form the core of a complete Telegram routing system.

### 🔴 Priority 1 — Core Routing and Request Flow

These features are the foundation of the package.

- [x] Telegram Webhook support
- [x] Telegram Polling support
- [x] Separate `routes/bot.php` routing file
- [x] Telegram update processing
- [ ] Command routing
- [ ] Message routing
- [ ] Callback Query routing
- [ ] Update-type detection and routing
- [ ] Route parameters
- [ ] Controller dispatching through the Laravel Service Container
- [ ] Dependency Injection support
- [ ] Telegram-specific route collection and matching

Example target API:

```php
Telegram::command('/start', StartController::class, 'index');
Telegram::message(MessageController::class, 'handle');
Telegram::callback('profile', ProfileController::class, 'show');
```

### 🔴 Priority 2 — Middleware

Middleware is one of the most important features for making the router feel like Laravel's HTTP router.

- [ ] Telegram middleware support
- [ ] Middleware pipeline
- [ ] Route middleware
- [ ] Middleware groups
- [ ] Global Telegram middleware
- [ ] Middleware parameters

Example target API:

```php
Telegram::middleware([
    CheckUser::class,
    IsAdmin::class,
])->command('/admin', AdminController::class, 'index');
```

### 🔴 Priority 3 — Route Groups

Route groups should make it possible to share middleware and other route settings across multiple Telegram routes.

- [ ] Route groups
- [ ] Shared middleware
- [ ] Route prefixes where applicable
- [ ] Nested route groups

Example target API:

```php
Telegram::group([
    'middleware' => [AdminMiddleware::class],
], function () {

    Telegram::command('/users', UserController::class);
    Telegram::command('/stats', StatsController::class);

});
```

### 🔴 Priority 4 — Conversation / State / Waiting for the Next Message ⭐⭐⭐

This is a key feature for interactive Telegram bots.

The bot must be able to ask the user for information and then wait for the user's next message. The next message should be treated as the answer to the current step instead of being handled as a normal unrelated message.

Example flow:

```text
Bot: Please enter your name.
User: Hossein
Bot: Please enter your phone number.
User: 0912...
```

The router must remember that the user is currently in a conversation and route the next message to the correct step.

Target API:

```php
Telegram::conversation('register')
    ->step(1, AskName::class)
    ->step(2, AskPhone::class)
    ->step(3, FinishRegister::class);
```

The state system should support:

- [ ] Per-user conversation state
- [ ] Conversation steps
- [ ] Waiting for the next message
- [ ] Saving the current step
- [ ] Moving to the next step
- [ ] Finishing a conversation
- [ ] Cancelling a conversation
- [ ] Conversation timeout
- [ ] Persistent state using Laravel Cache or another configurable storage driver
- [ ] Multiple independent conversations where practical

This feature is important for registration forms, multi-step menus, surveys, support bots, and other interactive workflows.

### 🔴 Priority 5 — Error and Exception Handling

The package should provide clear Telegram-specific exceptions and predictable error handling.

- [ ] Telegram route not found exception
- [ ] Invalid Telegram update exception
- [ ] Telegram API exception
- [ ] Router exception handling
- [ ] Configurable exception handler
- [ ] Safe error logging without exposing bot tokens or sensitive data

Possible exception classes:

```text
TelegramRouteNotFoundException
TelegramInvalidUpdateException
TelegramApiException
```

### 🟠 Priority 6 — Events

Integrate Telegram routing with Laravel's event system.

- [ ] Update received event
- [ ] Message received event
- [ ] Command received event
- [ ] Callback Query received event
- [ ] Route matched event
- [ ] Route dispatched event

Possible events:

```text
TelegramUpdateReceived
TelegramMessageReceived
TelegramCommandReceived
TelegramCallbackReceived
```

### 🟠 Priority 7 — Rate Limiting and Anti-Spam

Provide Laravel-compatible rate limiting for Telegram users and chats.

- [ ] Per-user rate limiting
- [ ] Per-chat rate limiting
- [ ] Command rate limiting
- [ ] Configurable limits
- [ ] Cache-based rate limiting

Example target:

```text
10 requests per user per minute
```

### 🟠 Priority 8 — Telegram Route List

Provide a command similar to Laravel's `route:list`.

```bash
php artisan telegram:route:list
```

Example output:

```text
COMMAND     /start       StartController@index
COMMAND     /help        HelpController@index
CALLBACK    profile      ProfileController@show
MESSAGE                  MessageController@handle
```

### 🟠 Priority 9 — Queue Support

Long-running or expensive Telegram operations should be able to use Laravel queues.

- [ ] Queue Telegram jobs
- [ ] Queue message sending
- [ ] Queue heavy update processing
- [ ] Laravel queue integration

This is especially useful for webhook applications where the webhook request should finish quickly.

### 🟡 Priority 10 — Route Names

Support named Telegram routes.

```php
Telegram::command('/start', StartController::class)
    ->name('telegram.start');
```

### 🟡 Priority 11 — Route Cache

Support caching Telegram routes for production environments.

```bash
php artisan telegram:route:cache
php artisan telegram:route:clear
```

### 🟡 Priority 12 — More Telegram Update Types

Expand routing beyond normal messages and commands.

- [ ] Inline Query
- [ ] Edited Message
- [ ] Channel Post
- [ ] Edited Channel Post
- [ ] Chat Member
- [ ] My Chat Member
- [ ] Chat Join Request
- [ ] Other Telegram update types as the Bot API evolves

### 🟡 Priority 13 — Inline Keyboard and Callback Routing

Provide clean APIs for building interactive Telegram buttons and connecting them to routes.

Possible target API:

```php
Telegram::callback('profile', ProfileController::class);
```

And, where useful, a fluent keyboard API for creating callback buttons.

### 🟡 Priority 14 — User and Chat Conditions

Allow routes and middleware to make decisions based on the Telegram user or chat.

Examples include:

- [ ] User-based authorization
- [ ] Admin-only routes
- [ ] Chat-type conditions
- [ ] Private-chat conditions
- [ ] Group-chat conditions
- [ ] Configurable route constraints

### 🟡 Priority 15 — Testing Tools

Provide testing helpers similar to Laravel's HTTP testing tools.

- [ ] Telegram fake
- [ ] Fake Telegram updates
- [ ] Route dispatch assertions
- [ ] Message sending assertions
- [ ] Callback assertions
- [ ] Conversation/state tests

Possible target API:

```php
Telegram::fake();

// Run the Telegram route...

Telegram::assertMessageSent(...);
```

## Architecture Direction

The package should keep a clear separation between Laravel HTTP routing and Telegram routing.

```text
Laravel HTTP
    routes/web.php
          │
          ▼
    Laravel Router

Telegram Bot
    routes/bot.php
          │
          ▼
 Telegram Router
          │
          ├── Middleware
          ├── Route Matching
          ├── Update Detection
          ├── Conversation / State
          └── Controller Dispatch
```

The main goal is to provide a familiar Laravel development experience while keeping Telegram bot logic separate from `web.php`.
