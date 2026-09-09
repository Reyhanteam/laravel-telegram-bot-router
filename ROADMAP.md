# 🗺️ ReyhanTeam Laravel Telegram Bot Router — Development Roadmap

> «A Laravel-native routing and management system for Telegram bots.»

این فایل نقشه‌ی اصلی توسعه‌ی پروژه است. اولویت‌ها بر اساس اهمیت فنی، وابستگی قابلیت‌ها و production-readiness تنظیم شده‌اند.

## وضعیت‌ها

| وضعیت | معنی |
|---|---|
| ✅ | پیاده‌سازی + بررسی + مستندات انجام شده |
| 🟡 | هسته وجود دارد اما هنوز باید کامل/تست/امن/مستند شود |
| ⬜ | هنوز پیاده‌سازی نشده |

## قانون اصلی

تا وقتی Completion Gate کامل نشده، قابلیت جدید بزرگی مثل Regex Callback Query شروع نمی‌شود. هر بخش باید از «کار می‌کند» به **کامل، تست‌شده، امن، مستند و production-ready** برسد.

---

# 🟢 Foundation

## 1. Core Telegram Routing — ✅

- ✅ Composer / Packagist / Service Provider
- ✅ `routes/bot.php`
- ✅ Webhook / Polling
- ✅ Command / Text / Callback Query routing
- ✅ Closure / Controller handlers
- ✅ Container resolution / Dependency Injection
- ✅ Exact matching / text regex
- ✅ Route parameters / constraints
- ✅ Command arguments
- ✅ Fallback / invalid-update handling
- ✅ Telegram update types

## 2. Middleware System — ✅

- ✅ Global / route middleware
- ✅ Groups / nested groups
- ✅ Parameters / aliases
- ✅ Container resolution
- ✅ Short-circuit / execution order
- ✅ `TelegramMiddlewareInterface`

## 3. Conversation & State — ✅

- ✅ Per-user / per-chat state
- ✅ Steps / next message / current step
- ✅ Finish / timeout / data
- ✅ Laravel Cache / configurable cache store
- ✅ Closure / Controller steps
- ✅ Cancel API / command
- ✅ Input validation helpers
- ✅ Explicit conversation middleware
- ✅ Conversation events

## 4. Events — ✅

- ✅ Update Received
- ✅ Message Received
- ✅ Command Received
- ✅ Callback Query Received
- ✅ Route Matched
- ✅ Conversation lifecycle events

## 5. Route Management & Conditions — ✅

- ✅ Route list / named routes
- ✅ Route cache / clear
- ✅ Admin-only
- ✅ User / chat conditions
- ✅ Private / group / channel conditions
- ✅ Permission checks
- ✅ Chat type constraints

---

# 🔴 Completion Gate — قبل از Featureهای جدید

## 6. Exception, Error Handling & Security Hardening — 🟡 CURRENT

### Exception handling

- ✅ Telegram route exceptions
- ✅ Invalid update exceptions
- ✅ Telegram API exceptions
- ✅ Configurable exception handler
- ✅ Safe logging foundation
- ✅ Sensitive-data sanitization foundation
- ✅ Token redaction in logs

### Webhook authentication / verification

- ✅ Configurable Telegram `secret_token`
- ✅ `X-Telegram-Bot-Api-Secret-Token` verification
- ✅ Constant-time comparison with `hash_equals()`
- ✅ Missing/invalid secret rejected with HTTP `401 Unauthorized`
- ✅ Verification occurs before JSON parsing and routing
- ✅ Empty secret preserves backwards compatibility by disabling verification
- ⬜ Verification test matrix — moved to Testing Gate

### Security documentation

- ✅ Security policy
- ✅ Production hardening guide
- ✅ Threat / abuse considerations
- ✅ Secret handling and rotation guidance
- ✅ Logging and sensitive-data policy
- ✅ Authorization vs webhook authentication guidance

### Remaining in this Gate

- 🟡 Webhook verification test suite
- 🟡 Final exception/security integration review

---

## 7. Queue Reliability — ✅

### موجود

- ✅ Laravel Queue integration
- ✅ Queued routes / update processing
- ✅ Attempts / backoff / timeout
- ✅ Queue middleware
- ✅ Deduplication foundation
- ✅ Failed-job hook
- ✅ `TelegramJobFailed` event
- ✅ Failure logging foundation
- ✅ Retry behavior test coverage
- ✅ Failed-job integration coverage
- ✅ Explicit retryable / non-retryable exception policy
- ✅ Failed-update persistence decision: Laravel `failed_jobs` is canonical; no duplicate package table
- ✅ Failed-job inspection context in logs/events
- ✅ Production queue documentation
- ✅ Real Laravel queue worker verification in the consuming application
- ✅ Multiple real Telegram updates through Polling + Queue
- ✅ Callback Query through Queue
- ✅ Message route through Queue
- ✅ Route-specific Queue
- ✅ Non-retryable exception behavior
- ✅ Retryable exception behavior
- ✅ Deduplication behavior

### Queue reliability flow

```text
Telegram Update
      ↓
Laravel Queue
      ↓
TelegramQueueJob
      ↓
Exception Policy
   ↙           ↘
retry          fail immediately
  ↓                 ↓
Laravel attempts    failed_jobs
  ↓                 ↓
backoff             TelegramJobFailed
  ↓
worker retry
```

### Completion note

Package-level Testbench coverage and real consuming-application worker verification have been completed. Queue dispatch, routing, retries, non-retryable failures, failed-job handling, route-specific queues and deduplication were verified in the Laravel test application.

## 8. Testing & Fake Telegram — 🟡

### موجود

- ✅ `Telegram::fake()`
- ✅ Fake API calls/responses
- ✅ Fake messages/commands/callback queries
- ✅ Fake incoming updates
- ✅ API/message/keyboard/callback assertions
- ✅ Controller execution coverage
- ✅ Webhook and Callback Query integration foundations

### Verified real Telegram API smoke tests

The following developer-facing Bot API methods were tested successfully against the real Telegram test bot:

- ✅ `getMe`
- ✅ `logOut`
- ✅ `close`
- ✅ `sendMessage`
- ✅ `sendMessageDraft`
- ✅ `sendPhoto`
- ✅ `sendAudio`
- ✅ `sendDocument`
- ✅ `sendVideo`
- ✅ `sendAnimation`

### باقی‌مانده

- 🟡 Webhook secret verification tests
- 🟡 Full route-testing helpers
- 🟡 Middleware integration coverage
- 🟡 Conversation integration coverage
- 🟡 Rate-limit integration coverage
- 🟡 Invalid-update matrix
- 🟡 Route parameter/constraint matrix
- 🟡 Polling testable architecture
- 🟡 Complete PHPUnit/Testbench suite
- 🟡 Public testing documentation

## 9. Telegram API & Response Layer — 🟡

### موجود

- ✅ Telegram API client
- ✅ API method registry
- ✅ Developer-friendly API facade foundation
- ✅ Real Telegram API smoke tests for `getMe`, `logOut`, `close`, `sendMessage`, `sendMessageDraft`, `sendPhoto`, `sendAudio`, `sendDocument`, `sendVideo`, and `sendAnimation`

### باقی‌مانده

- 🟡 Unified high-level Response API
- 🟡 Response objects / fluent helpers
- 🟡 Consistent controller/closure return handling
- 🟡 Reply markup / parse mode helpers
- 🟡 Reply-to-message helpers
- 🟡 Edit/delete helpers
- 🟡 Media abstraction
- 🟡 Response tests / documentation

## 10. Keyboard Builder — 🟡

### موجود

- ✅ Inline / Reply keyboards
- ✅ Callback / URL / WebApp / Login buttons
- ✅ Switch-inline / Pay buttons
- ✅ Rows / chaining
- ✅ Dynamic / conditional buttons
- ✅ Factories
- ✅ Callback-data helper
- ✅ Validation / JSON serialization

### باقی‌مانده

- 🟡 Comprehensive Telegram compatibility tests
- 🟡 Edge-case validation tests
- 🟡 Reusable keyboard patterns
- 🟡 Final Response API integration
- 🟡 Documentation

## 11. Outgoing Telegram Rate Limiter — 🟡

### موجود

- ✅ Per-user / per-chat / per-command limits
- ✅ Configurable limits
- ✅ Laravel Cache / RateLimiter integration

### باقی‌مانده

- 🟡 Outgoing API throttling
- 🟡 Queue-aware throttling
- 🟡 Telegram `retry_after` handling
- 🟡 Backoff integration
- 🟡 Tests / documentation

---

# 🟠 ترتیب Completion Gate

1. Exception + Webhook Security — 🟡
2. Queue Reliability — ✅
3. Testing / Fake Telegram — 🟡
4. Telegram Response API — 🟡
5. Keyboard Completion — 🟡
6. Outgoing Rate Limiter — 🟡

### Definition of Done

```text
Implementation
   ↓
Unit Tests
   ↓
Integration Tests
   ↓
Edge Cases
   ↓
Security / Reliability Review
   ↓
Documentation
   ↓
Stable API
   ↓
Real Worker Verification
   ↓
✅ 100%
```

---

# 🚀 Feature Roadmap — بعد از Completion Gate

## 12. Regex Callback Query Routing — ⬜ NEXT

```php
BOT::onCallbackQuery(
    '/^user:(\\d+)$/',
    [UserController::class, 'show']
);
```

- ⬜ Regex detection
- ⬜ Capture groups
- ⬜ Named capture groups
- ⬜ `$update->matches`
- ⬜ Interaction with callback parameters
- ⬜ Constraints compatibility
- ⬜ Exact/regex route scoring
- ⬜ Unit + integration tests
- ⬜ Documentation

## 13. Bot Context — ⬜
- ⬜ Current update / User / Chat
- ⬜ Current route / parameters / command arguments
- ⬜ Conversation / middleware state
- ⬜ API client / bot information

## 14. Pagination — ⬜
- ⬜ Previous / Next / page numbers
- ⬜ Callback pagination
- ⬜ Collection integration
- ⬜ Custom and state-safe pagination

## 15. Forms & Wizards — ⬜
- ⬜ Multi-step forms
- ⬜ Validation
- ⬜ Previous / Next / Cancel / Confirmation
- ⬜ Conversation integration

## 16. User & Chat Abstraction — ⬜
- ⬜ Telegram User / Chat abstraction
- ⬜ Metadata / state
- ⬜ Laravel integration

## 17. Deep Links — ⬜
- ⬜ `/start` parameters
- ⬜ Referral / campaign tracking
- ⬜ Onboarding helpers

## 18. Admin & Permission System — ⬜
- ⬜ Roles / permissions
- ⬜ Command / chat / moderation permissions

## 19. Group Moderation — ⬜
- ⬜ Ban / unban / restrict
- ⬜ Warnings
- ⬜ Link / word filtering
- ⬜ Moderation logs

## 20. Auto Moderation — ⬜
- ⬜ Rule engine
- ⬜ Regex / spam rules
- ⬜ Automatic actions

## 21. Topics & Forums — ⬜
- ⬜ Topic detection / routing
- ⬜ Topic permissions / conversations

## 22. Telegram WebApp / Mini Apps — ⬜
- ⬜ Init data validation
- ⬜ Authentication / user integration
- ⬜ Secure backend communication

## 23. Payments — ⬜
- ⬜ Invoices / payment updates
- ⬜ Provider integration / events

## 24. Broadcast System — ⬜
- ⬜ Batch sending / segmentation
- ⬜ Scheduling / queues
- ⬜ Delivery/failure tracking
- ⬜ Rate limiting

## 25. Multi-Bot — ⬜
- ⬜ Multiple tokens
- ⬜ Bot-specific routes/middleware/configuration/conversations
- ⬜ Bot management

## 26. Plugin / Module Architecture — ⬜
- ⬜ Modules / plugins
- ⬜ Lifecycle / configuration
- ⬜ Routes / middleware / events

---

# 🛠️ Developer Experience & Operations

## 27. Debug & Inspector — ⬜
- ⬜ Update inspector
- ⬜ Route / middleware / conversation inspection
- ⬜ API diagnostics

## 28. Telegram Doctor — ⬜
```bash
php artisan telegram:doctor
```
- ⬜ Token / API / webhook / polling checks
- ⬜ Routes / cache / queue / permissions / configuration

## 29. CI/CD & Compatibility — ⬜
- ⬜ GitHub Actions matrix
- ⬜ PHP / Laravel compatibility
- ⬜ Static analysis / code style / dependency checks
- ⬜ Automated releases

## 30. Documentation & Examples — ⬜
- ⬜ Documentation website
- ⬜ English / Persian documentation
- ⬜ API reference
- ⬜ Installation / webhook / polling / routing
- ⬜ Middleware / conversations / queue / testing
- ⬜ Keyboard / Response API
- ⬜ Example applications

## 31. AI-Friendly Documentation — ⬜
- ⬜ Structured API reference
- ⬜ Machine-readable examples
- ⬜ Task recipes
- ⬜ Troubleshooting knowledge base

---

# 📍 CURRENT POSITION

```text
Completion Gate
      ↓
1. Exception + Webhook Security       ← CURRENT
      ↓
2. Queue Reliability                  ← COMPLETE
      ↓
3. Testing / Fake Telegram
      ↓
4. Response API
      ↓
5. Keyboard Completion
      ↓
6. Outgoing Rate Limiter
      ↓
Regex Callback Query Routing
```

> Queue Reliability is complete at package and consuming-application verification level. Ten real Telegram Bot API methods have also been smoke-tested successfully in the Laravel test application.
