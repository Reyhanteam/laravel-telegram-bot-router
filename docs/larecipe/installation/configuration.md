# Configuration

Laravel Telegram Bot Router keeps its package configuration in:

```text
config/telegram-bot-router.php
```

If the configuration file has not been published yet, run:

```bash
php artisan vendor:publish --tag=telegram-bot-config
```

## Environment configuration

Sensitive and environment-specific values should be stored in your application's `.env` file rather than hard-coded in PHP source files.

For example:

```env
TELEGRAM_BOT_TOKEN=your-bot-token
```

## Webhook secret

For webhook deployments, you can configure Telegram's secret token verification:

```env
TELEGRAM_WEBHOOK_SECRET_TOKEN=replace-with-a-random-secret
```

When a secret is configured, incoming webhook requests must contain the expected `X-Telegram-Bot-Api-Secret-Token` header.

The package validates the secret before parsing and routing the Telegram update. Missing or invalid secrets are rejected with HTTP `401 Unauthorized`.

## Queue configuration

If queued Telegram updates are enabled, configure the queue connection and queue name according to your Laravel application's queue setup.

Example environment values:

```env
TELEGRAM_QUEUE_CONNECTION=database
TELEGRAM_QUEUE_NAME=default
TELEGRAM_QUEUE_TRIES=3
TELEGRAM_QUEUE_BACKOFF=1,1,1
TELEGRAM_QUEUE_TIMEOUT=120
```

Use the queue connection and queue name that exist in your Laravel application.

## Cache and conversation state

Conversation and state features can use Laravel Cache. Configure the appropriate cache store in the package configuration when your application needs a specific store.

## Configuration principle

The package configuration should describe application behavior and integration settings. Keep secrets in environment variables and keep bot route definitions in:

```text
routes/bot.php
```

This separation makes the application easier to maintain and deploy across development, testing, and production environments.

## Next step

Continue with [Routing](../routing/introduction.md) to learn how to build Telegram bot routes.
