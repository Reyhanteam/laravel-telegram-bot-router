# Installation

Laravel Telegram Bot Router is installed as a Composer package inside an existing Laravel application.

## 1. Install the package

From the root directory of your Laravel application, run:

```bash
composer require reyhanteam/laravel-telegram-bot-router
```

Composer installs the package and Laravel can then discover its service provider through the normal package discovery process.

## 2. Publish the bot routes

Publish the package's Telegram route file:

```bash
php artisan vendor:publish --tag=telegram-bot-routes
```

This creates:

```text
routes/bot.php
```

Your Telegram routes can now be defined separately from Laravel's normal HTTP routes in `routes/web.php`.

## 3. Publish the configuration

Publish the package configuration with:

```bash
php artisan vendor:publish --tag=telegram-bot-config
```

The configuration file is published to:

```text
config/telegram-bot-router.php
```

Review the configuration before running your bot.

## 4. Configure the bot token

Add your Telegram bot token to the application's environment configuration.

For example:

```env
TELEGRAM_BOT_TOKEN=your-bot-token
```

Never commit a real bot token to source control.

## 5. Define your first route

Open:

```text
routes/bot.php
```

Then define a Telegram route:

```php
use App\Http\Controllers\Telegram\StartController;
use ReyhanTeam\TelegramBotRouter\Facades\Route;

Route::onCommand('start', [StartController::class, 'index']);
```

When Telegram sends `/start`, the router matches the command and dispatches it to `StartController::index()`.

## 6. Choose a transport

You can receive updates through webhook or polling.

### Polling

Run:

```bash
php artisan reyhan:start-polling
```

### Webhook

Register the package webhook route with:

```bash
php artisan reyhan:setWebhookRoute
```

The default endpoint is:

```text
POST /telegram/webhook
```

For production, configure the webhook secret token and use a publicly reachable HTTPS endpoint.

## Installation complete

At this point the package is installed, Telegram routes are separated from Laravel HTTP routes, and your bot can use the package routing layer.

Continue with [Configuration](configuration.md) or go to the [Routing](../routing/introduction.md) guide to learn how Telegram routes work.
