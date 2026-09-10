---
title: How It Works
---
# How It Works

Telegram sends an update through either webhook or polling. The package passes the update to its update manager, matches it against the routes in `routes/bot.php`, applies middleware and route conditions, and dispatches the selected callback or controller method.
