---
title: FAQ
---
# FAQ

## Webhook or polling?

Use webhook when your Laravel application can receive public HTTPS requests. Use polling when a worker can continuously retrieve updates.

## Where do Telegram routes live?

Publish the package route file and define bot routes in `routes/bot.php`.
