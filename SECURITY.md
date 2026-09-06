# Security Policy

## Scope

This package is a Laravel-native router for Telegram Bot API updates. Security controls in this document cover webhook authentication, secret handling, update processing, logging, and production deployment.

## Supported Security Model

The package supports Telegram webhook authentication through Telegram's `secret_token` mechanism.

Configure the secret in the Laravel environment:

```env
TELEGRAM_WEBHOOK_SECRET_TOKEN=replace-with-a-random-secret
```

The value is available through:

```php
'webhook' => [
    'secret_token' => env('TELEGRAM_WEBHOOK_SECRET_TOKEN', ''),
],
```

When the value is non-empty, every webhook request must contain the HTTP header:

```text
X-Telegram-Bot-Api-Secret-Token
```

The package compares the configured and received values with `hash_equals()` so the comparison is performed in constant time.

Missing and incorrect secrets are rejected with HTTP `401 Unauthorized` before the request body is parsed or dispatched to the Telegram router.

When the configured secret is empty, verification is disabled for backwards compatibility. Production webhook deployments should configure a secret.

## Configuring Telegram

When creating or updating a Telegram webhook, provide the same secret to Telegram as the package configuration. Telegram will then send it in the `X-Telegram-Bot-Api-Secret-Token` header on webhook requests.

The secret should be treated as a credential:

- Generate a high-entropy random value.
- Store it in `.env` or a dedicated secret manager.
- Never commit it to Git.
- Never put it in a public route, README example, screenshot, issue, or log.
- Do not reuse the Telegram bot token as the webhook secret.
- Rotate it if it may have been exposed.

## Production Hardening Checklist

Before exposing a webhook endpoint to the public internet:

- [ ] Set `TELEGRAM_BOT_MODE=webhook`.
- [ ] Configure `TELEGRAM_WEBHOOK_SECRET_TOKEN` with a strong random value.
- [ ] Configure Telegram with the same `secret_token`.
- [ ] Serve the application over HTTPS.
- [ ] Keep `TELEGRAM_BOT_TOKEN` out of source control and logs.
- [ ] Keep `APP_DEBUG=false` in production.
- [ ] Keep Laravel, PHP, and package dependencies updated.
- [ ] Run the webhook behind a production-grade web server and PHP runtime.
- [ ] Apply normal Laravel request, authentication, authorization, and rate-limiting controls where appropriate.
- [ ] Keep webhook responses free of Telegram tokens, webhook secrets, request headers, and raw sensitive payloads.
- [ ] Monitor application and queue failures without recording credentials.
- [ ] Restrict filesystem and environment access using the host/container permissions appropriate for the deployment.
- [ ] Review proxy/load-balancer configuration so the Telegram secret header is forwarded unchanged to Laravel.

## Threat and Abuse Considerations

### Forged webhook requests

An attacker can discover the public webhook URL and send arbitrary HTTP requests to it. A configured Telegram webhook secret prevents an attacker who does not know the secret from reaching update processing.

The package performs this check before JSON parsing and routing. Applications should still treat Telegram update content as untrusted input and validate business-level authorization inside routes and middleware.

### Replay and duplicate delivery

Webhook authentication proves knowledge of the secret; it does not make an update non-replayable. Telegram delivery and application retries can also produce duplicate processing scenarios. Applications that perform non-idempotent actions should use the package's deduplication/queue facilities where appropriate and design handlers to be idempotent.

### Sensitive information in logs

Do not log the Telegram bot token, webhook secret, authorization headers, cookies, or complete raw updates when they may contain sensitive application data. Prefer structured logs containing safe identifiers such as `update_id`, route names, and processing status.

### Denial-of-service and malformed input

A secret check is an authentication control, not a general denial-of-service solution. Use HTTPS, a properly configured web server/reverse proxy, sensible request limits, Laravel/application rate limiting where appropriate, and operational monitoring. Invalid requests must not expose stack traces or internal configuration.

### Authorization

Webhook authentication only establishes that the request contains the configured Telegram webhook secret. It does not make a Telegram user an administrator. User/chat permissions must be enforced separately through application authorization and Telegram route middleware.

## Secret Rotation

To rotate the webhook secret safely:

1. Generate a new random secret.
2. Update `TELEGRAM_WEBHOOK_SECRET_TOKEN` in the production environment.
3. Clear/reload Laravel's configuration using the deployment procedure for the application.
4. Update the Telegram webhook to use the new secret.
5. Verify that requests with the new secret are accepted.
6. Verify that the old secret is rejected.

Because the package compares against the currently configured value, the old secret stops being accepted after the application configuration is updated.

## Reporting a Security Issue

Please do not disclose an unpatched security vulnerability in a public GitHub issue. Contact the project maintainers privately with:

- a concise description of the issue,
- affected package version(s),
- reproduction steps or a minimal proof of concept,
- expected and actual behavior, and
- any relevant security impact.

Do not include real bot tokens, webhook secrets, personal data, or other credentials in a report.
