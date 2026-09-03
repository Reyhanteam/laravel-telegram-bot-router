# 🚀 Next Steps — Telegram Bot Router

This document is the implementation plan for the next major development stages of the package.

The goal is to finish the most important Core capabilities first, make the package production-ready, then build the official documentation and move toward advanced Telegram features.

> **Status in this document is based on a direct audit of the current Repository implementation. A checked item means the corresponding capability exists in the current codebase; an unchecked item means it is not currently implemented as a dedicated capability.**

---

## 🔴 Priority 14 — Advanced Callback Query Routing ⭐⭐⭐

**First priority to implement.**

Goal: make Callback Query routing as powerful and structured as the package's other routing systems.

- [x] Named callback routes
- [x] Callback route parameters
- [x] Inline keyboard integration
- [x] Exact callback data matching
- [ ] Regular expression callback matching
- [x] Callback parameter constraints
- [x] Multiple callback parameters
- [ ] Callback route groups
- [x] Callback middleware integration
- [x] Callback route fallback
- [x] Callback route priority / matching order
- [x] Callback route caching support

Example:

```php
BOT::onCallbackQuery(
    'user/{id}/profile',
    [ProfileController::class, 'show']
);

BOT::onCallbackQuery(
    'product/{id}/buy',
    [ProductController::class, 'buy']
);
```

---

## 🔴 Priority 15 — Queue Reliability ⭐⭐⭐

The core Queue system already exists. This priority makes Queue processing production-ready and reliable.

- [x] Queue update processing
- [x] Queue message sending
- [x] Queue heavy bot tasks
- [x] Laravel Queue integration
- [ ] Queue retry handling
- [ ] Configurable retry attempts
- [ ] Retry delay / backoff
- [ ] Failed Telegram jobs
- [ ] Failed job handling
- [ ] Failed job events
- [ ] Queue exception handling
- [ ] Job timeout configuration
- [ ] Job middleware support
- [ ] Prevent duplicate update processing
- [ ] Queue logging
- [x] Queue configuration

> **Audit note:** queue message sending has a `SendTelegramMessageJob`, but its current implementation still references the removed `irazasyed/telegram-bot-sdk` facade. It is therefore considered an existing queue implementation, but it still needs to be migrated to the package's current Telegram API client before it can be considered production-ready.

Example:

```php
BOT::onCommand(
    'start',
    [StartController::class, 'index']
)->queue();
```

---

## 🔴 Priority 16 — Testing & Fake Telegram ⭐⭐⭐⭐⭐

This priority creates the testing foundation for the package. Every future feature should be covered by automated tests where appropriate.

- [ ] Telegram Fake
- [ ] Fake Telegram API
- [ ] Fake incoming updates
- [ ] Fake messages
- [ ] Fake callback queries
- [ ] Fake commands
- [ ] Fake users
- [ ] Fake chats
- [ ] Route testing helpers
- [ ] Command route assertions
- [ ] Text route assertions
- [ ] Callback route assertions
- [x] Middleware testing
- [x] Conversation testing
- [ ] Event testing
- [ ] Queue testing
- [x] Rate limit testing
- [ ] Webhook testing
- [ ] Polling testing
- [x] Telegram API response mocking
- [ ] Assert sent messages
- [ ] Assert sent keyboards
- [ ] Assert callbacks
- [ ] Assert controller execution
- [x] PHPUnit integration
- [x] Laravel testing integration
- [x] Example test suite

Example target API:

```php
Telegram::fake();

$this->post('/telegram/webhook', [
    'message' => [
        'text' => '/start',
    ],
]);

Telegram::assertCommandReceived('start');
Telegram::assertMessageSent('Welcome!');
```

---

## 🟠 Priority 17 — Bot Context ⭐⭐⭐⭐

Goal: provide a clean, consistent context object containing the current Telegram update and its related data.

> The Repository currently provides `TelegramUpdate` with accessors such as `chatId()`, `userId()`, `messageId()`, `text()`, `callbackQueryData()`, `commandArguments()`, and route-parameter access. A dedicated `TelegramContext` abstraction is not implemented yet, so the context-specific checklist below remains unchecked.

- [ ] Telegram Bot Context
- [ ] Current Update
- [ ] Current User
- [ ] Current Chat
- [ ] Current Message
- [ ] Current Callback Query
- [ ] Current Command
- [ ] Current Route
- [ ] Current Route Parameters
- [ ] Current Bot
- [ ] Context injection
- [ ] Context access from Controller
- [ ] Context access from Middleware
- [ ] Context access from Conversation
- [ ] Context access from Events
- [ ] Context helper methods
- [ ] Context lifecycle
- [ ] Context isolation per update

Example:

```php
public function index(TelegramContext $context)
{
    $user = $context->user();
    $chat = $context->chat();
    $message = $context->message();
}
```

---

## 🟠 Priority 18 — Telegram Response API ⭐⭐⭐⭐

Goal: provide a clean Laravel-friendly API for sending and managing Telegram responses.

- [ ] Telegram response API
- [x] Send message
- [x] Reply to message
- [x] Edit message
- [x] Delete message
- [x] Send photo
- [x] Send video
- [x] Send audio
- [x] Send document
- [x] Send animation
- [x] Send voice
- [x] Send location
- [x] Send contact
- [x] Send venue
- [x] Send sticker
- [x] Answer callback query
- [x] Edit callback message
- [x] Chat action
- [x] Parse mode support
- [x] Reply markup support
- [x] Inline keyboard support
- [x] Reply keyboard support
- [x] Force reply support
- [x] Message options
- [ ] Response objects
- [ ] Response chaining
- [ ] Queued responses

> **Audit note:** the package now has a large developer-friendly `BOT::...` Telegram API surface with typed static methods and optional/named arguments. The dedicated higher-level `TelegramResponse` abstraction, response objects, response chaining, and current-client queued responses are not implemented yet.

Example:

```php
return Telegram::sendMessage(
    'سلام 👋'
);
```

---

## 🟠 Priority 19 — Keyboard Builder ⭐⭐⭐⭐⭐

This priority should follow the Response API because keyboards are closely integrated with Telegram responses and Callback Query routing.

- [ ] Inline Keyboard Builder
- [ ] Reply Keyboard Builder
- [ ] Inline buttons
- [ ] Callback buttons
- [ ] URL buttons
- [ ] WebApp buttons
- [ ] Login buttons
- [ ] Switch inline query buttons
- [ ] Callback data helpers
- [ ] Keyboard rows
- [ ] Multiple rows
- [ ] Button chaining
- [ ] Dynamic buttons
- [ ] Conditional buttons
- [ ] Keyboard factories
- [ ] Reusable keyboards
- [ ] Keyboard validation
- [ ] Keyboard integration with routes
- [ ] Keyboard integration with callbacks
- [ ] Keyboard integration with responses
- [ ] Keyboard testing helpers

> **Audit note:** keyboard structures can currently be passed directly as Telegram `reply_markup` arrays, including inline and reply keyboards, but there is no dedicated `Keyboard`/builder abstraction with chaining, factories, validation, or testing helpers.

Example:

```php
return Telegram::reply(
    'انتخاب کنید:',
    Keyboard::inline()
        ->button('پروفایل', 'profile')
        ->button('تنظیمات', 'settings')
        ->row()
        ->button('وب‌سایت', 'https://example.com')
);
```

---

# 🧭 Recommended Implementation Order

Implement the priorities in this exact order:

1. **Priority 14 — Advanced Callback Query Routing**
2. **Priority 15 — Queue Reliability**
3. **Priority 16 — Testing & Fake Telegram**
4. **Priority 17 — Bot Context**
5. **Priority 18 — Telegram Response API**
6. **Priority 19 — Keyboard Builder**

Dependency direction:

```text
Advanced Callback Routing
          ↓
Queue Reliability
          ↓
Testing / Fake Telegram
          ↓
Bot Context
          ↓
Telegram Response API
          ↓
Keyboard Builder
```

---

# 📚 After These Six Priorities

Once these six priorities are completed, the recommended next phase is the **official Documentation Website** for ReyhanTeam.

```text
Core Routing
      ↓
Middleware
      ↓
Conversation
      ↓
Events
      ↓
Rate Limiting
      ↓
Queue
      ↓
Route Cache
      ↓
Update Types
      ↓
User / Chat Conditions
      ↓
Callback Routing
      ↓
The Six Priorities Above
      ↓
════════════════════
    Stable Core
════════════════════
      ↓
📚 Documentation
      ↓
🌐 reyhanteam.ir
      ↓
🧪 Example Applications
      ↓
🚀 Advanced Features
```

After the documentation and example applications are established, continue with larger roadmap areas such as:

- Pagination
- Forms & Wizards
- Telegram UI Components
- Media API
- Deep Links
- Telegram WebApp
- Payments
- Broadcast System
- Admin & Permission System
- Group Moderation
- Auto Moderation
- Topics & Forums
- Multi-Bot
- Plugin / Module Architecture
- Debug & Inspector
- Telegram Doctor
- Security
- CI/CD & Compatibility
- AI-Friendly Documentation
- Community & Ecosystem
- SEO & Global Discovery
- ReyhanTeam Ecosystem

---

## 🎯 Current Next Task

**Start with Priority 14 — Advanced Callback Query Routing.**

Do not move to the documentation website until these six core priorities have been reviewed and the Core is stable enough to document accurately.
