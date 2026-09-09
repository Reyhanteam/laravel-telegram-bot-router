# Callback Query Routing

Priority 12 adds structured routing for Telegram callback queries.

## Exact callback data

```php
BOT::onCallback('buy', [ShopController::class, 'buy']);
```

Only the exact callback data `buy` matches this route.

The existing method also accepts the pattern form:

```php
BOT::onCallbackQuery('buy', [ShopController::class, 'buy']);
```

The legacy generic form remains supported:

```php
BOT::onCallbackQuery([CallbackController::class, 'handle']);
```

## Callback route parameters

Use `{name}` placeholders to capture values from callback data:

```php
BOT::onCallback('product:{id}', [ProductController::class, 'show']);
```

For callback data `product:125`, the route parameter is:

```php
['id' => '125']
```

It is available through:

```php
$id = $update->routeParameter('id');
```

Or through a controller method:

```php
public function show($update, $id)
{
    // $id === '125'
}
```

Constraints work with callback parameters:

```php
BOT::onCallback('product:{id}', [ProductController::class, 'show'])
    ->whereNumber('id');
```

## Named callback routes

```php
BOT::onCallback('product:{id}', [ProductController::class, 'show'])
    ->name('product.show');
```

The route can be inspected with:

```php
$route = BOT::getRouteByName('product.show');
```

## Named routes from Inline Keyboard

A named route can also be a command or another Telegram route. This lets an Inline Keyboard button reuse the same Closure or controller action.

```php
use ReyhanTeam\TelegramBotRouter\Facades\BOT;
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;

BOT::onCommand('start', [TelegramTestController::class, 'start'])
    ->name('start');

$keyboard = Keyboard::inline()
    ->callbackButton('👤 پروفایل', Keyboard::routeName('start'));
```

`Keyboard::routeName('start')` produces the callback data `start`.

When Telegram sends the callback query, the router uses the named route alias. The original route action is then executed. This works with both Closure routes and controller actions.

The named route keeps its original middleware, rate-limit, and queue configuration.

An unknown route name is rejected when `Keyboard::routeName()` is called.

## Inline Keyboard integration for callback routes

Use a named callback route to generate callback data for an Inline Keyboard button:

```php
$callbackData = BOT::callbackRoute('product.show', [
    'id' => 125,
]);
```

The result is:

```text
product:125
```

This avoids duplicating callback-data formats in different parts of the application.

Example:

```php
$keyboard = [
    [
        [
            'text' => 'Product details',
            'callback_data' => BOT::callbackRoute('product.show', ['id' => 125]),
        ],
    ],
];
```

## Regex callback matching

Regular-expression callback matching is intentionally **not implemented** in Priority 12.

Callback routes use exact values or `{parameter}` route patterns. Regular expressions remain available for text routes.
