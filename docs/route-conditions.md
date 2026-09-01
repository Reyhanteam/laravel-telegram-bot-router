# Telegram Route Conditions

Telegram routes can be restricted by user and chat context.

## Admin-only routes

Use `adminOnly()` to allow only Telegram chat owners and administrators.

```php
BOT::onCommand('addproduct', [ProductController::class, 'create'])
    ->adminOnly();
```

The router checks the sender with Telegram `getChatMember`.

## User conditions

Use `whereUser()` when a route must be available only to specific Telegram user IDs.

```php
BOT::onCommand('test-payment', [PaymentController::class, 'test'])
    ->whereUser(123456789);
```

Multiple IDs are supported:

```php
->whereUser([123456789, 987654321]);
```

## Private, group, and channel routes

```php
BOT::onCommand('checkout', [OrderController::class, 'checkout'])
    ->privateChat();

BOT::onCommand('discount', [ShopController::class, 'discount'])
    ->groupChat();

BOT::onCommand('announce', [ChannelController::class, 'announce'])
    ->channel();
```

`groupChat()` accepts both Telegram `group` and `supergroup` chats.

## Chat type constraints

Use `chatType()` when more control is needed:

```php
->chatType('private');
->chatType(['group', 'supergroup']);
```

Valid chat types are `private`, `group`, `supergroup`, and `channel`.

## User permission checks

Use `userPermission()` for Telegram administrator permission fields such as `can_delete_messages` or `can_manage_chat`.

```php
BOT::onCommand('delete', [AdminController::class, 'delete'])
    ->userPermission('can_delete_messages');
```

Multiple permissions require all listed permissions:

```php
->userPermission(['can_delete_messages', 'can_manage_chat']);
```

Chat owners pass permission checks automatically.

## Combining conditions

Conditions can be chained:

```php
BOT::onCommand('admin', [AdminController::class, 'panel'])
    ->adminOnly()
    ->privateChat();
```

This means the command is available only to a Telegram chat administrator or owner and only in a private chat.

## Available methods

| Method | Purpose |
| --- | --- |
| `adminOnly()` | Chat owner or administrator only |
| `whereUser($id)` | Restrict to specific Telegram user IDs |
| `privateChat()` | Private chats only |
| `groupChat()` | Groups and supergroups only |
| `channel()` | Channels only |
| `chatType($type)` | One or more exact Telegram chat types |
| `userPermission($permission)` | Require Telegram administrator permission(s) |
| `permission($permission)` | Alias for `userPermission()` |
| `user($id)` | Alias for `whereUser()` |
