# Keyboard Builder

`Keyboard` provides a fluent builder for Telegram inline and reply keyboards.

```php
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;

$keyboard = Keyboard::inline()
    ->button('پروفایل', 'profile')
    ->button('تنظیمات', 'settings')
    ->row()
    ->button('وب‌سایت', 'https://example.com');

BOT::sendMessage(
    $chatId,
    'انتخاب کنید:',
    replyMarkup: $keyboard->toArray(),
);
```

## Inline keyboards

Supported button types:

- callback buttons with `callbackButton()`
- URL buttons with `url()`
- Web App buttons with `webApp()`
- Login buttons with `login()`
- switch inline query buttons with `switchInlineQuery()`
- switch inline query in current chat with `switchInlineQueryCurrentChat()`
- payment buttons with `pay()`
- `button($text, $value)` as a convenience method. A valid URL becomes a URL button. Other values become callback data.

Rows are created by chaining buttons. Call `row()` to start the next row.

## Reply keyboards

```php
$keyboard = Keyboard::reply()
    ->button('بله')
    ->button('خیر')
    ->row()
    ->button('بعداً')
    ->resize()
    ->oneTime()
    ->persistent();
```

Reply keyboard options include `resize()`, `oneTime()`, `persistent()`, `selective()`, and `placeholder()`.

Use `remove()` to build a remove-keyboard markup or `forceReply()` for Telegram ForceReply.

## Dynamic and conditional buttons

Use `when()` and `unless()` for conditional UI:

```php
$keyboard = Keyboard::inline()
    ->button('خانه', 'home')
    ->when($user->isAdmin(), fn (Keyboard $keyboard) =>
        $keyboard->button('مدیریت', 'admin')
    )
    ->unless($user->isVerified(), fn (Keyboard $keyboard) =>
        $keyboard->button('تأیید حساب', 'verify')
    );
```

## Callback data helpers

`callbackData()` creates compact route-style callback data and validates the Telegram 1–64 byte limit:

```php
$data = Keyboard::callbackData('profile', ['id' => 42]);
// profile?id=42

$keyboard = Keyboard::inline()
    ->callbackButton('پروفایل', $data);
```

The generated value is sent as Telegram `callback_data`. It can therefore be matched by the package callback-query routes. The builder does not change the router's matching rules.

## Factories and reusable keyboards

A keyboard can be created in a reusable factory:

```php
final class MainMenu
{
    public static function make(): Keyboard
    {
        return Keyboard::inline()
            ->button('پروفایل', 'profile')
            ->button('تنظیمات', 'settings');
    }
}
```

Or with the built-in factory helper:

```php
$keyboard = Keyboard::factory(
    fn (Keyboard $keyboard) => $keyboard
        ->button('خانه', 'home')
        ->button('راهنما', 'help')
);
```

## Validation and testing

`validate()` checks the builder structure. `toArray()` also validates before returning Telegram's native `reply_markup` array. `jsonSerialize()` and `__toString()` are available for snapshot-style tests and debugging.

The builder does not make HTTP requests. This keeps keyboard tests deterministic and separate from real Telegram API tests.

## Response integration

The current Bot API facade uses typed `array` parameters for `replyMarkup`. Therefore, pass the builder with `->toArray()`:

```php
return BOT::sendMessage(
    $chatId,
    'انتخاب کنید:',
    replyMarkup: Keyboard::inline()
        ->button('پروفایل', 'profile')
        ->button('تنظیمات', 'settings')
        ->row()
        ->url('وب‌سایت', 'https://example.com')
        ->toArray(),
);
```

This preserves the existing array API while adding the builder abstraction. Direct object support in the facade signature can be added as part of the Response API work without changing the builder contract.
