# Laravel Telegram Bot Router

## Version 1.3.2

A Laravel-native Telegram Bot API router. Telegram routes live in `routes/bot.php`, separate from Laravel HTTP routes in `routes/web.php`.

```text
routes/web.php  -> Laravel HTTP routes
routes/bot.php  -> Telegram bot routes
```

## Features

- Webhook and polling support
- Laravel `routes/bot.php`
- Command, text and callback-query routing
- Controller and Closure handlers
- Laravel Service Container / dependency injection
- Exact routes, text regex, route parameters and constraints
- Command arguments
- Fallback and invalid-update handling
- Telegram `TelegramUpdate` wrapper
- Global, group and route middleware
- Middleware aliases and parameters
- Conversations, steps, state, cancellation and validation
- Conversation events and configurable cache storage
- Route list/cache/clear commands
- Admin/user/chat conditions and permissions
- Telegram API client and developer-friendly API facade
- Queued update processing with attempts, backoff, timeout and deduplication
- Explicit retryable/non-retryable queue exception policy
- `TelegramJobFailed` event and failure inspection context
- Fake Telegram API and incoming-update testing helpers
- Keyboard builder foundation
- Webhook secret-token authentication
- Security policy and production hardening guidance

## Installation

```bash
composer require reyhanteam/laravel-telegram-bot-router
```

Publish configuration and Telegram routes:

```bash
php artisan vendor:publish --tag=telegram-bot-config
php artisan vendor:publish --tag=telegram-bot-routes
```

This creates:

```text
routes/bot.php
```

## Routing

```php
use ReyhanTeam\TelegramBotRouter\Facades\BOT;

BOT::onCommand('start', [StartController::class, 'index']);
BOT::onText('hello', [MessageController::class, 'hello']);
BOT::onCallbackQuery([ProfileController::class, 'show']);
```

Controller dependencies are resolved through Laravel's Service Container.

## Webhook

The default endpoint is:

```text
POST /telegram/webhook
```

Register the route explicitly with:

```bash
php artisan reyhan:setWebhookRoute
```

### Webhook authentication

Production deployments should configure Telegram's webhook secret token:

```env
TELEGRAM_WEBHOOK_SECRET_TOKEN=replace-with-a-random-secret
```

When configured, every webhook request must contain:

```text
X-Telegram-Bot-Api-Secret-Token
```

The package validates the header before parsing or routing the update and compares the values with constant-time `hash_equals()`. Missing or incorrect secrets receive HTTP `401 Unauthorized`.

An empty secret keeps verification disabled for backwards compatibility. Do not use that mode for a production public webhook.

For the full security policy, production hardening checklist, threat/abuse considerations and secret-rotation procedure, read [`SECURITY.md`](SECURITY.md).

## Polling

```bash
php artisan reyhan:start-polling
```

Polling and webhook updates use the same routing layer.

## Route parameters and constraints

```php
BOT::onCommand('user {id}', [UserController::class, 'show'])
    ->whereNumber('id');
```

For `/user 123`, the controller can receive `$id === '123'` or read it from:

```php
$update->routeParameter('id');
```

Text routes support regular expressions:

```php
BOT::onText('/^hello/i', [MessageController::class, 'hello']);
```

Regex captures are available through `$update->matches`.

## Middleware

```php
BOT::middleware([
    CheckUser::class,
    IsAdmin::class,
])->onCommand('admin', [AdminController::class, 'index']);
```

Global and grouped middleware are also supported.

## Conversations

```php
BOT::conversation('register')
    ->step([RegisterController::class, 'name'])
    ->step([RegisterController::class, 'phone'])
    ->startOnCommand('register');
```

Conversation state is stored through Laravel Cache and can be configured per store.

## Telegram API

The package exposes the Telegram API through its developer-friendly facade. API calls can be faked in package tests without making network requests.

## Queue processing

Updates can be processed through Laravel Queue with configurable connection, queue name, attempts, backoff, timeout and deduplication settings.

Queue jobs also apply an explicit exception policy. Non-retryable exceptions can fail immediately. Failed jobs use Laravel's `failed_jobs` storage as the canonical persistence layer and emit `TelegramJobFailed` with inspection context.

See [`docs/queue-reliability.md`](docs/queue-reliability.md) for retry policy, failed-job inspection, worker verification and production operation.

## Keyboard

The keyboard builder supports Inline and Reply keyboards, callback/URL/WebApp/Login buttons, switch-inline buttons, rows, chaining, dynamic/conditional buttons, factories, callback-data helpers and validation.

## Configuration

The main configuration file is:

```text
config/telegram-bot-router.php
```

Important environment values include:

```env
TELEGRAM_BOT_TOKEN=
TELEGRAM_BOT_MODE=webhook
TELEGRAM_WEBHOOK_PATH=/telegram/webhook
TELEGRAM_WEBHOOK_SECRET_TOKEN=
TELEGRAM_QUEUE_UPDATES=false
TELEGRAM_QUEUE_CONNECTION=
TELEGRAM_QUEUE_NAME=default
TELEGRAM_QUEUE_TRIES=3
TELEGRAM_QUEUE_BACKOFF=10,30,60
TELEGRAM_QUEUE_TIMEOUT=120
```

Never commit bot tokens or webhook secrets to source control.

## Security

Security controls are designed in layers:

```text
Public webhook request
        ↓
Webhook secret verification
        ↓
JSON/update validation
        ↓
Telegram router
        ↓
Middleware / authorization
        ↓
Controller or Closure
```

Webhook authentication does not replace application authorization. User, chat and admin permissions must still be enforced by the application's route conditions or middleware.

See [`SECURITY.md`](SECURITY.md) before deploying a public webhook.

## Development roadmap

See [`ROADMAP.md`](ROADMAP.md) for the implementation status, completion gates and upcoming features.

## License

See [`composer.json`](composer.json) for the package license.