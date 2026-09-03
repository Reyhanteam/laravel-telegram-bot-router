# 🚀 Next Steps — Telegram Bot Router

This document is the implementation plan for the next major development stages of the package.

The goal is to finish the most important Core capabilities first, make the package production-ready, then build the official documentation and move toward advanced Telegram features.

---

## 🔴 Priority 14 — Advanced Callback Query Routing ⭐⭐⭐

**First priority to implement.**

Goal: make Callback Query routing as powerful and structured as the package's other routing systems.

- [x] Named callback routes
- [x] Callback route parameters
- [x] Inline keyboard integration
- [ ] Exact callback data matching
- [ ] Regular expression callback matching
- [ ] Callback parameter constraints
- [ ] Multiple callback parameters
- [ ] Callback route groups
- [ ] Callback middleware integration
- [ ] Callback route fallback
- [ ] Callback route priority / matching order
- [ ] Callback route caching support

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
- [ ] Queue configuration

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
- [ ] Middleware testing
- [ ] Conversation testing
- [ ] Event testing
- [ ] Queue testing
- [ ] Rate limit testing
- [ ] Webhook testing
- [ ] Polling testing
- [ ] Telegram API response mocking
- [ ] Assert sent messages
- [ ] Assert sent keyboards
- [ ] Assert callbacks
- [ ] Assert controller execution
- [ ] PHPUnit integration
- [ ] Laravel testing integration
- [ ] Example test suite

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
- [ ] Send message
- [ ] Reply to message
- [ ] Edit message
- [ ] Delete message
- [ ] Send photo
- [ ] Send video
- [ ] Send audio
- [ ] Send document
- [ ] Send animation
- [ ] Send voice
- [ ] Send location
- [ ] Send contact
- [ ] Send venue
- [ ] Send sticker
- [ ] Answer callback query
- [ ] Edit callback message
- [ ] Chat action
- [ ] Parse mode support
- [ ] Reply markup support
- [ ] Inline keyboard support
- [ ] Reply keyboard support
- [ ] Force reply support
- [ ] Message options
- [ ] Response objects
- [ ] Response chaining
- [ ] Queued responses

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
