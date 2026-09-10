# Requirements

## Laravel

Laravel Telegram Bot Router is designed specifically for Laravel applications.

Before installing the package, make sure your application uses a Laravel version supported by the current package release.

## PHP

Your PHP version must satisfy the PHP requirement declared by the installed package version.

You can check your application's PHP version with:

```bash
php -v
```

## Telegram Bot

You also need a Telegram bot created through Telegram's official bot management system and its bot token.

Keep the bot token private. Do not commit it to your repository or expose it in public documentation, source code, or client-side applications.

## Application transport

The package supports two ways to receive Telegram updates:

- **Webhook** — Telegram sends updates to your Laravel application.
- **Polling** — your application continuously retrieves updates from Telegram.

Webhook deployments require a publicly reachable HTTPS endpoint. Polling does not require a public webhook endpoint.

## Laravel services

The package integrates with Laravel features such as:

- Service Container
- Artisan commands
- Cache
- Queue
- Middleware
- Events
- Configuration

These integrations use the Laravel application's own infrastructure.

## Next step

Once the requirements are satisfied, continue with the [Installation](installation.md) guide.
