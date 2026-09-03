# 🗺️ ReyhanTeam Laravel Telegram Bot Router — Roadmap

> «A Laravel-native routing and management system for Telegram bots.»

این Roadmap مسیر توسعه‌ی `ReyhanTeam/laravel-telegram-bot-router` را از هسته‌ی Routing تا تبدیل شدن به یک اکوسیستم کامل برای توسعه‌ی ربات‌های Telegram در Laravel مشخص می‌کند.

> **وضعیت این Roadmap بر اساس بررسی مستقیم Repository و کد فعلی پروژه تنظیم شده است، نه صرفاً بر اساس حدس یا نسخه‌ی قبلی Roadmap.**

---

## 📊 Current Status

| وضعیت | معنی |
|---|---|
| ✅ Completed | پیاده‌سازی شده و قابل استفاده است |
| 🟡 In Progress | بخشی از قابلیت پیاده‌سازی شده است |
| ⬜ Planned | هنوز در برنامه‌ی توسعه قرار دارد |

---

## 🚀 Phase 1 — Core Telegram Routing

«هسته‌ی اصلی Router»

| Feature | Status |
|---|---|
| Laravel Composer Package | ✅ |
| GitHub Repository | ✅ |
| Packagist | ✅ |
| Telegram Webhook | ✅ |
| Telegram Polling | ✅ |
| `routes/bot.php` | ✅ |
| `BOT::onCommand()` | ✅ |
| `BOT::onText()` | ✅ |
| `BOT::onCallbackQuery()` | ✅ |
| Closure Handlers | ✅ |
| Controller + Method Handlers | ✅ |
| Laravel Service Container Resolution | ✅ |
| Dependency Injection | ✅ |
| Regular Expression Matching for Text Routes | ✅ |
| Route Parameters | ✅ |
| Route Constraints | ✅ |
| Command Arguments | ✅ |
| `TelegramUpdate` Wrapper | ✅ |
| Route Matching Engine | ✅ |
| Fallback Routes | ✅ |
| Invalid Update Handling | ✅ |

---

## 🛡️ Phase 2 — Middleware

«Laravel-style middleware pipeline for Telegram updates.»

| Feature | Status |
|---|---|
| Middleware Pipeline | ✅ |
| Route Middleware | ✅ |
| Global Telegram Middleware | ✅ |
| Middleware Object Resolution | ✅ |
| Laravel Container Resolution | ✅ |
| Middleware Short-Circuit | ✅ |
| Middleware Execution Order | ✅ |
| `TelegramMiddlewareInterface` | ✅ |
| Middleware Groups | ✅ |
| Nested Middleware Groups | ✅ |
| Middleware Parameters | ✅ |
| Named Middleware Aliases | ✅ |
| Middleware Configuration | ✅ |

---

## 💬 Phase 3 — Conversation & State

«Build multi-step Telegram conversations.»

| Feature | Status |
|---|---|
| Per-user Conversation State | ✅ |
| Conversation Steps | ✅ |
| Wait for Next Message | ✅ |
| Save Current Step | ✅ |
| Move to Next Step | ✅ |
| Finish Conversation | ✅ |
| Conversation Timeout | ✅ |
| Conversation Data | ✅ |
| Laravel Cache Storage | ✅ |
| Closure Conversation Steps | ✅ |
| Controller Conversation Steps | ✅ |
| Cancel Conversation API | ⬜ |
| Input Validation Helpers | ⬜ |
| Explicit Conversation Middleware | ⬜ |
| Conversation Events | ⬜ |
| Additional Storage Drivers | ⬜ |

---

## ⚠️ Phase 4 — Exception & Error Handling

«Reliable error handling for production Telegram bots.»

| Feature | Status |
|---|---|
| Telegram Route Exceptions | ✅ |
| Invalid Update Exceptions | ✅ |
| Telegram API Exceptions | ✅ |
| Configurable Exception Handler | ✅ |
| Safe Logging | ✅ |
| Sensitive Data Protection | ✅ |
| Never Expose Bot Tokens in Logs | ✅ |

---

## 📡 Phase 5 — Events

«Laravel-style events for Telegram applications.»

| Event | Status |
|---|---|
| Update Received | ✅ |
| Message Received | ✅ |
| Command Received | ✅ |
| Callback Query Received | ✅ |
| Conversation Started | ✅ |
| Conversation Step | ✅ |
| Conversation Finished | ✅ |
| Route Matched | ✅ |

---

## 🚦 Phase 6 — Rate Limiting

«Protect bots and Telegram API usage.»

| Feature | Status |
|---|---|
| Per-user Rate Limit | ✅ |
| Per-chat Rate Limit | ✅ |
| Per-command Rate Limit | ✅ |
| Configurable Limits | ✅ |
| Laravel Cache / RateLimiter Integration | ✅ |
| Outgoing Telegram Rate Limiter | ⬜ |

---

## 🧭 Phase 7 — Telegram Route Management

«Make Telegram routes feel like native Laravel routes.»

### Route List

```bash
php artisan reyhan:route-list
```

Status: **✅**

### Named Telegram Routes

```php
BOT::onCommand('start', [StartController::class, 'index'])
    ->name('telegram.start');
```

Status: **✅**

### Telegram Route Cache

```bash
php artisan telegram:route:cache
php artisan telegram:route:clear
```

Status: **✅**

---

## 📨 Phase 8 — More Telegram Update Types

«Expand routing beyond normal messages and callback queries.»

| Update Type | Status |
|---|---|
| Inline Query | ✅ |
| Edited Message | ✅ |
| Channel Post | ✅ |
| Edited Channel Post | ✅ |
| Chat Member | ✅ |
| My Chat Member | ✅ |
| Chat Join Request | ✅ |

---

## 🔘 Phase 9 — Better Callback Query Routing

«More powerful callback query routing.»

| Feature | Status |
|---|---|
| Exact Callback Data Matching | ✅ |
| Regex Callback Matching | ⬜ |
| Callback Route Parameters | ✅ |
| Named Callback Routes | ✅ |
| Inline Keyboard Integration | ✅ |

> Regex matching برای Callback Query در Repository صراحتاً هنوز پیاده‌سازی نشده است. Regex همچنان برای Text Routes در دسترس است.

---

## 👤 Phase 10 — User & Chat Conditions

«Control who can access Telegram routes.»

| Feature | Status |
|---|---|
| Admin-only Routes | ✅ |
| User Conditions | ✅ |
| Private Chat Conditions | ✅ |
| Group Chat Conditions | ✅ |
| Channel Conditions | ✅ |
| User Permission Checks | ✅ |
| Chat Type Constraints | ✅ |

نمونه APIهای فعلی:

```php
BOT::onCommand('admin', [AdminController::class, 'panel'])
    ->adminOnly();

BOT::onCommand('test', [TestController::class, 'handle'])
    ->whereUser(123456789);

BOT::onCommand('checkout', [OrderController::class, 'checkout'])
    ->privateChat();

BOT::onCommand('delete', [AdminController::class, 'delete'])
    ->userPermission('can_delete_messages');
```

---

## 🧠 Phase 11 — Bot Context

«Provide a unified context for every Telegram update.»

Planned capabilities:

- User information
- Chat information
- Current update
- Current route
- Current conversation
- Middleware state
- Shared request data
- Telegram client access

Status: ⬜

---

## 💬 Phase 12 — Telegram Response API

«Simplify sending Telegram responses from routes and controllers.»

Planned API:

```php
return TelegramResponse::text('Hello!');
return TelegramResponse::photo($photo);
return TelegramResponse::document($document);
```

Status: ⬜

---

## ⌨️ Phase 13 — Keyboard Builder

«Laravel-friendly keyboard construction.»

Planned support:

- Inline keyboards
- Reply keyboards
- Callback buttons
- URL buttons
- WebApp buttons
- Dynamic keyboards
- Keyboard factories

Status: ⬜

---

## 📄 Phase 14 — Pagination

«Native pagination for Telegram messages and keyboards.»

Planned features:

- Previous / Next
- Page numbers
- Callback-based pagination
- Custom pagination views
- Laravel Collection integration

Status: ⬜

---

## 🧙 Phase 15 — Forms & Wizards

«Build multi-step Telegram forms easily.»

Example:

```text
Name
 ↓
Phone
 ↓
Email
 ↓
Confirmation
 ↓
Complete
```

Planned features:

- Multi-step forms
- Validation
- Previous step
- Next step
- Cancel
- Confirmation
- Conversation integration

Status: ⬜

---

## 🧩 Phase 16 — Telegram UI Components

«Reusable UI components for Telegram bots.»

Planned components:

- Menus
- Buttons
- Select menus
- Confirm dialogs
- Pagination
- Forms
- Wizards
- Navigation components

Status: ⬜

---

## 📁 Phase 17 — Media API

«Unified API for Telegram media.»

Planned support:

- Photos
- Videos
- Documents
- Audio
- Voice
- Stickers
- Albums
- File downloads
- File storage integration

Status: ⬜

---

## 👥 Phase 18 — User & Chat Abstraction

«Application-level abstractions for Telegram users and chats.»

Planned features:

- Telegram User model
- Telegram Chat model
- User metadata
- Chat metadata
- User state
- Chat state
- Laravel integration

Status: ⬜

---

## 🔗 Phase 19 — Deep Links

«Support Telegram deep linking.»

Examples:

```text
/start referral_123
https://t.me/example_bot?start=referral_123
```

Planned features:

- Start parameters
- Referral tracking
- Campaign parameters
- User onboarding

Status: ⬜

---

## 🌐 Phase 20 — Telegram WebApp

«Build Telegram Mini Apps / Web Apps with Laravel.»

Planned features:

- WebApp authentication
- Init data validation
- User integration
- Secure communication
- Laravel backend integration

Status: ⬜

---

## 💳 Phase 21 — Payments

«Simplify Telegram payment workflows.»

Planned capabilities:

- Invoice handling
- Payment updates
- Successful payments
- Provider integration
- Order integration
- Payment events

Status: ⬜

---

## 📢 Phase 22 — Broadcast System

«Build scalable Telegram notification systems.»

Planned features:

- Broadcast messages
- User segmentation
- Batch sending
- Scheduling
- Queue integration
- Delivery tracking
- Failure handling
- Rate limiting

Status: ⬜

---

## 👮 Phase 23 — Admin & Permission System

«Build powerful Telegram administration tools.»

Planned features:

- Admin detection
- Permission checks
- Role system
- Chat permissions
- Command permissions
- Moderation permissions

Status: ⬜

---

## 🛡️ Phase 24 — Group Moderation

«Tools for building Telegram group administration bots.»

Planned capabilities:

- Ban / unban
- Restrict users
- Warning system
- Spam detection
- Link filtering
- Word filtering
- User management
- Moderation logs

Status: ⬜

---

## 🤖 Phase 25 — Auto Moderation

«Rule-based Telegram moderation engine.»

Example:

```text
Message
   ↓
Moderation Rules
   ↓
Spam?
   ├── Yes → Action
   └── No  → Continue
```

Planned features:

- Rule engine
- Custom rules
- Regex rules
- Spam rules
- Automatic actions
- Warning thresholds

Status: ⬜

---

## 🧵 Phase 26 — Topics & Forums

«Support Telegram forum groups and topics.»

Planned features:

- Topic detection
- Topic routing
- Topic-specific commands
- Topic permissions
- Topic-aware conversations

Status: ⬜

---

## 🔄 Phase 27 — Queue Integration

«Process Telegram updates and heavy tasks asynchronously.»

Planned features beyond the current queue core:

- Queue update processing | **Already implemented in core**
- Queue message sending | **Already implemented in core**
- Heavy task processing | **Already supported through queued routes/jobs**
- Laravel Queue integration | **Already implemented in core**
- Retry handling | ⬜
- Failed jobs | ⬜

Overall Status: **🟡 In Progress**

---

## 🤖 Phase 28 — Multi-Bot

«Run multiple Telegram bots inside one Laravel application.»

Planned features:

- Multiple bot tokens
- Bot-specific routes
- Bot-specific configuration
- Bot-specific middleware
- Bot-specific conversations
- Bot management

Status: ⬜

---

## 🧩 Phase 29 — Plugin / Module Architecture

«Allow developers to extend the router without modifying the core.»

Planned features:

- Modules
- Plugins
- Plugin lifecycle
- Plugin configuration
- Plugin routes
- Plugin middleware
- Plugin events

Status: ⬜

---

## 🧪 Phase 30 — Testing & Fake Telegram

«First-class testing tools for Telegram bots.»

Planned API:

```php
Telegram::fake();
Telegram::sendMessage(...);
Telegram::assertMessageSent(...);
```

Planned features:

- Telegram Fake
- Route testing
- Update testing
- Middleware testing
- Conversation testing
- Callback testing
- API assertions

Status: ⬜

---

## 🔍 Phase 31 — Debug & Inspector

«Developer tools for debugging Telegram bots.»

Planned features:

- Update inspector
- Matched route
- Middleware pipeline
- Conversation state
- API requests
- API responses
- Debug mode
- Error diagnostics

Status: ⬜

---

## 🩺 Phase 32 — Telegram Doctor

«Diagnose common configuration problems.»

Example:

```bash
php artisan telegram:doctor
```

Potential checks:

- Bot token
- Telegram API connection
- Webhook configuration
- Polling configuration
- Route configuration
- Cache
- Queue
- Permissions
- Laravel configuration

Status: ⬜

---

## 🔐 Phase 33 — Security

«Security-first Telegram bot development.»

Planned features:

- Token protection
- Secure logging
- Webhook verification
- Input validation
- Rate limiting
- Permission validation
- Security documentation
- Security policy

Status: ⬜

---

## 🔄 Phase 34 — CI/CD & Compatibility

«Maintain compatibility across supported Laravel and PHP versions.»

Planned:

- GitHub Actions
- Automated tests
- PHP version matrix
- Laravel version matrix
- Static analysis
- Code style checks
- Dependency checks
- Automated releases

Status: ⬜

---

## 📚 Phase 35 — Documentation

«Professional documentation for developers.»

| Documentation | Status |
|---|---|
| Documentation Website | ⬜ |
| English Documentation | ⬜ |
| Persian Documentation | ⬜ |
| API Reference | ⬜ |
| Installation Guide | ⬜ |
| Configuration Guide | ⬜ |
| Webhook Guide | ⬜ |
| Polling Guide | ⬜ |
| Routing Guide | ⬜ |
| Controllers Guide | ⬜ |
| Middleware Guide | ⬜ |
| Conversations Guide | ⬜ |
| Callbacks Guide | ⬜ |
| Queues Guide | ⬜ |
| Testing Guide | ⬜ |
| Deployment Guide | ⬜ |
| Security Guide | ⬜ |

---

## 🛒 Phase 36 — Example Applications

«Provide real-world examples.»

### Shop Bot

Status: ⬜

Potential features:

- Products
- Categories
- Cart
- Orders
- Payments
- User accounts
- Pagination

### Group Admin Bot

Status: ⬜

Potential features:

- Admin commands
- Moderation
- User permissions
- Anti-spam
- Logs

### All-in-One Bot

Status: ⬜

Demonstrates:

- Commands
- Messages
- Callbacks
- Conversations
- Middleware
- Keyboard
- Pagination
- Admin tools
- WebApp

### Demo Bot

Status: ⬜

---

## 🤖 Phase 37 — AI-Friendly Documentation

«Make the project easy for AI assistants and search engines to understand.»

Planned:

- Structured documentation
- Clear API reference
- Machine-readable examples
- `llms.txt`
- `llms-full.txt`
- AI-friendly documentation pages
- Accurate package metadata
- GitHub examples

Status: ⬜

---

## 🌍 Phase 38 — Community & Ecosystem

«Build a real developer community around the project.»

Planned:

- GitHub Discussions
- GitHub Issues
- Contribution Guide
- Code of Conduct
- Security Policy
- Changelog
- Release notes
- Developer tutorials
- Blog posts
- YouTube tutorials
- Community examples
- Contributors

Status: ⬜

---

## 📈 Phase 39 — SEO & Global Discovery

«Make ReyhanTeam discoverable by Laravel and Telegram developers worldwide.»

Target topics:

- Laravel Telegram Bot
- Laravel Telegram Bot Router
- Telegram Bot Laravel
- Laravel Telegram Webhook
- Laravel Telegram Polling
- Telegram Routing Laravel
- PHP Telegram Bot Laravel

Planned:

- Documentation SEO
- Structured metadata
- Search-friendly examples
- Tutorials
- Backlinks
- Community references

Status: ⬜

---

## 🏗️ Phase 40 — ReyhanTeam Ecosystem

«Build a complete ecosystem around Telegram development with Laravel.»

Long-term vision:

```text
                    ReyhanTeam
                         │
        ┌────────────────┼────────────────┐
        │                │                │
   Telegram Router   Documentation    Developer Tools
        │                │                │
        ├── Routing     ├── Guides      ├── Testing
        ├── Middleware ├── API Docs    ├── Inspector
        ├── Events     ├── Examples    └── Doctor
        ├── Queue      └── AI Docs
        └── Conversations
```

Status: ⬜

---

## 🎯 Development Priority

برای توسعه‌ی بعدی، پیشنهاد می‌شود ابتدا قابلیت‌های زیر تکمیل شوند:

1. Regex Callback Matching
2. تکمیل Queue با Retry و Failed Jobs
3. Testing & Fake Telegram
4. Bot Context
5. Telegram Response API
6. Keyboard Builder
7. Pagination
8. Forms & Wizards
9. Admin & Permission System پیشرفته
10. Group Moderation
11. Broadcast System
12. Multi-Bot
13. Documentation Website
14. AI-Friendly Documentation

---

## 🏁 Final Vision

هدف نهایی `ReyhanTeam/laravel-telegram-bot-router` فقط ساخت یک Telegram Router نیست.

هدف این است که توسعه‌دهنده‌ی Laravel بتواند تقریباً تمام منطق یک Telegram Bot را با همان فلسفه‌ای که در Laravel برای Routing، Middleware، Controller، Event، Queue و Service Container دارد، به شکلی تمیز، قابل توسعه و استاندارد مدیریت کند.

> **ReyhanTeam — Building a better Laravel experience for Telegram bots.**
