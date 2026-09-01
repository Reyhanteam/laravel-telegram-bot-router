# Telegram Update Types

Priority 11 adds routing support for Telegram update types beyond normal messages and callback queries.

## Supported routes

```php
use ReyhanTeam\TelegramBotRouter\TelegramBot as BOT;

BOT::onInlineQuery('windows 11', [SearchController::class, 'inline']);
BOT::onEditedMessage('Windows 11 Pro', [MessageController::class, 'edited']);
BOT::onChannelPost('New license', [ChannelController::class, 'post']);
BOT::onEditedChannelPost('Updated license', [ChannelController::class, 'edited']);
BOT::onChatMember('member', [MemberController::class, 'changed']);
BOT::onMyChatMember('administrator', [BotController::class, 'permissionsChanged']);
BOT::onChatJoinRequest([JoinRequestController::class, 'handle']);
```

A route without a pattern matches any update of that type. For the member routes, a pattern matches `new_chat_member.status`. For inline queries it matches `inline_query.query`. For edited messages and channel posts it matches the update text. For join requests, a pattern matches the invite-link URL when one is present.

## Controller example

```php
public function inline($update)
{
    $query = $update->inline_query->query;
}

public function changed($update)
{
    $status = $update->chat_member->new_chat_member->status;
}

public function handle($update)
{
    $userId = $update->userId();
    $chatId = $update->chatId();
}
```

`TelegramUpdate` now exposes common `chatId()`, `userId()`, `messageId()`, and `text()` values for these update types while keeping access to the complete raw update through `originalUpdate()`.
