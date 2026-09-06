# 🗺️ ReyhanTeam Laravel Telegram Bot Router — Development Roadmap

> «A Laravel-native routing and management system for Telegram bots.»

این فایل نقشه‌ی اصلی توسعه‌ی پروژه است. ترتیب مراحل بر اساس **اهمیت فنی، وابستگی قابلیت‌ها و production-readiness** تنظیم شده است.

## قانون اصلی

تا وقتی یک بخش دارای 🟡 باشد، قابلیت جدید مهمی را که به آن وابسته است شروع نمی‌کنیم. ابتدا قابلیت‌های موجود را از «کار می‌کند» به **کامل، تست‌شده، امن، مستند و production-ready** می‌رسانیم.

| وضعیت | معنی |
|---|---|
| ✅ | پیاده‌سازی + تست + رفتار پایدار |
| 🟡 | هسته وجود دارد اما هنوز باید کامل/تست/امن/مستند شود |
| ⬜ | هنوز پیاده‌سازی نشده |

---

# 🟢 Foundation فعلی

## 1. Core Telegram Routing — ✅

- ✅ Composer Package / Packagist / Service Provider
- ✅ `routes/bot.php`
- ✅ Webhook
- ✅ Polling
- ✅ Command / Text / Callback Query routes
- ✅ Closure و Controller handlers
- ✅ Container resolution و Dependency Injection
- ✅ `TelegramUpdate`
- ✅ Exact matching
- ✅ Text regex matching
- ✅ Route parameters / constraints
- ✅ Command arguments
- ✅ Fallback و invalid-update handling
- ✅ Update type routing

### Update Types

Message, Callback Query, Inline Query, Edited Message, Channel Post, Edited Channel Post, Chat Member, My Chat Member, Chat Join Request — همگی در هسته پشتیبانی می‌شوند.

> Regex برای Callback Query هنوز قابلیت جدید بعدی است و عمداً در Backlog قرار دارد.

---

## 2. Middleware System — ✅

- ✅ Global / route middleware
- ✅ Groups / nested groups
- ✅ Parameters
- ✅ Named aliases
- ✅ Container resolution
- ✅ Short-circuit
- ✅ Execution order
- ✅ `TelegramMiddlewareInterface`

---

## 3. Conversation & State — ✅

- ✅ Per-user / per-chat state
- ✅ Steps / next message / current step
- ✅ Finish / timeout / data
- ✅ Laravel Cache
- ✅ Closure و Controller steps
- ✅ Cancel API / command
- ✅ Input validation helpers
- ✅ Explicit conversation middleware
- ✅ Conversation events
- ✅ Storage driver / cache store

---

## 4. Events — ✅

- ✅ Update Received
- ✅ Message Received
- ✅ Command Received
- ✅ Callback Query Received
- ✅ Route Matched
- ✅ Conversation Started / Step Completed / Finished / Cancelled / Timed Out

---

## 5. Route Management & Conditions — ✅

- ✅ Route list
- ✅ Named routes
- ✅ Route cache / clear
- ✅ Admin-only
- ✅ User / chat conditions
- ✅ Private / group / channel conditions
- ✅ Permission checks
- ✅ Chat type constraints

---

# 🔴 Completion Gate — اول این‌ها باید 100٪ شوند

> **این بخش، وضعیت فعلی پروژه است. قبل از Regex Callback هیچ Feature بزرگ جدیدی شروع نمی‌شود.**

## 6. Exception, Error Handling & Security Hardening — 🟡 CURRENT

### انجام شده

- ✅ Telegram route exceptions
- ✅ Invalid update exceptions
- ✅ Telegram API exceptions
- ✅ Configurable exception handler
- ✅ Safe logging foundation
- ✅ Sensitive-data sanitization foundation
- ✅ Token redaction in logs

### باقی‌مانده

- 🟡 Webhook authentication / verification
- 🟡 Telegram `secret_token` verification
- 🟡 Constant-time secret comparison
- 🟡 Correct 401/403 behavior
- 🟡 Verification tests
- 🟡 Security configuration
- 🟡 Security policy
- 🟡 Production hardening guide
- 🟡 Threat / abuse considerations

### خروجی نهایی

```text
Telegram Webhook
      ↓
Secret Verification
      ↓
 invalid → 401/403
      ↓
 valid
      ↓
Parse Update → Router
```

---

## 7. Queue Reliability — 🟡

### الان واقعاً وجود دارد

- ✅ Laravel Queue integration
- ✅ Queue update processing
- ✅ Queued routes
- ✅ Attempts / tries configuration
- ✅ Backoff configuration
- ✅ Timeout configuration
- ✅ Queue middleware
- ✅ Deduplication foundation
- ✅ Failed job hook
- ✅ `TelegramJobFailed` event
- ✅ Failure logging foundation

### برای 100٪ باقی مانده

- 🟡 Retry behavior tests
- 🟡 Failed-job integration tests
- 🟡 Retryable / non-retryable exception policy
- 🟡 Failed update persistence/inspection decision
- 🟡 Verification against real Laravel queue worker behavior
- 🟡 Production queue documentation

> بنابراین Roadmap قدیمی که Retry/Backoff را «پیاده نشده» می‌دانست، دیگر معتبر نیست؛ بخش زیادی از آن همین حالا در کد وجود دارد.

---

## 8. Testing & Fake Telegram — 🟡

### انجام شده

- ✅ `Telegram::fake()`
- ✅ Fake API calls
- ✅ Fake API responses
- ✅ Fake messages / commands / callback queries
- ✅ Fake incoming updates
- ✅ API / message / keyboard / callback assertions
- ✅ Controller execution coverage
- ✅ Webhook integration tests
- ✅ Callback Query integration tests

### برای 100٪ باقی مانده

- 🟡 Full route-testing helpers
- 🟡 Middleware integration coverage
- 🟡 Conversation integration coverage
- 🟡 Queue integration coverage
- 🟡 Rate-limit integration coverage
- 🟡 Invalid-update test matrix
- 🟡 Route parameter/constraint matrix
- 🟡 Polling testable architecture
- 🟡 Complete PHPUnit/Testbench suite
- 🟡 Public testing examples/documentation

> تست‌های Callback Query و Webhook اخیراً اضافه شده‌اند و Callback Query واقعی با Polling نیز قبلاً تأیید شده است.

---

## 9. Telegram API & Response Layer — 🟡

### انجام شده

- ✅ Telegram API client
- ✅ API method registry
- ✅ Developer-friendly API method surface / facade foundation

### برای 100٪ باقی مانده

- 🟡 Unified high-level Response API
- 🟡 Response objects
- 🟡 Fluent response helpers
- 🟡 Consistent controller/closure return handling
- 🟡 Reply markup integration
- 🟡 Parse mode helpers
- 🟡 Reply-to-message helpers
- 🟡 Edit/delete helpers
- 🟡 Media abstraction
- 🟡 Response tests
- 🟡 Response documentation

---

## 10. Keyboard Builder — 🟡

Implementation فعلی از Roadmap قدیمی جلوتر است و شامل Inline/Reply، callback، URL، WebApp، Login، switch-inline، pay، rows، dynamic/conditional، factory و validation است.

### موجود

- ✅ Inline keyboard
- ✅ Reply keyboard
- ✅ Callback buttons
- ✅ URL buttons
- ✅ WebApp buttons
- ✅ Login buttons
- ✅ Switch inline query buttons
- ✅ Switch inline query current chat
- ✅ Pay button
- ✅ Rows / chaining
- ✅ Dynamic / conditional buttons
- ✅ Keyboard factories
- ✅ Callback-data helper
- ✅ Validation
- ✅ JSON serialization

### برای 100٪ باقی مانده

- 🟡 Comprehensive Telegram compatibility tests
- 🟡 Edge-case validation tests
- 🟡 Reusable keyboard patterns
- 🟡 Final Response API integration
- 🟡 Documentation

---

## 11. Outgoing Telegram Rate Limiter — 🟡

### موجود

- ✅ Per-user rate limit
- ✅ Per-chat rate limit
- ✅ Per-command rate limit
- ✅ Configurable limits
- ✅ Laravel Cache / RateLimiter integration

### باقی مانده

- 🟡 Outgoing API throttling
- 🟡 Queue-aware throttling
- 🟡 Telegram `retry_after` handling
- 🟡 Backoff integration
- 🟡 Tests
- 🟡 Documentation

---

# 🟠 ترتیب اجرای Completion Gate

**دقیقاً به این ترتیب جلو می‌رویم:**

1. Exception + Webhook Security
2. Queue Reliability
3. Testing / Fake Telegram
4. Telegram Response API
5. Keyboard completion
6. Outgoing Rate Limiter

### Definition of Done برای هر مورد

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
✅ 100%
```

---

# 🚀 بعد از Completion Gate — Feature Roadmap

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

---

## 13. Bot Context — ⬜

- ⬜ Current update
- ⬜ User / Chat
- ⬜ Current route
- ⬜ Route parameters
- ⬜ Command arguments
- ⬜ Conversation state
- ⬜ Middleware/shared state
- ⬜ Telegram API client
- ⬜ Bot information

---

## 14. Pagination — ⬜

- ⬜ Previous / Next
- ⬜ Page numbers
- ⬜ Callback pagination
- ⬜ Collection integration
- ⬜ Custom pagination
- ⬜ State-safe pagination

---

## 15. Forms & Wizards — ⬜

- ⬜ Multi-step forms
- ⬜ Validation
- ⬜ Previous / Next
- ⬜ Cancel
- ⬜ Confirmation
- ⬜ Conversation integration

---

## 16. User & Chat Abstraction — ⬜

- ⬜ Telegram User abstraction
- ⬜ Telegram Chat abstraction
- ⬜ Metadata
- ⬜ User/chat state
- ⬜ Laravel integration

---

## 17. Deep Links — ⬜

- ⬜ `/start` parameters
- ⬜ Referral tracking
- ⬜ Campaign parameters
- ⬜ Onboarding helpers

---

## 18. Admin & Permission System — ⬜

- ⬜ Roles
- ⬜ Permissions
- ⬜ Command permissions
- ⬜ Chat permissions
- ⬜ Moderation permissions

---

## 19. Group Moderation — ⬜

- ⬜ Ban / unban
- ⬜ Restrict users
- ⬜ Warning system
- ⬜ Link / word filtering
- ⬜ Moderation logs

---

## 20. Auto Moderation — ⬜

- ⬜ Rule engine
- ⬜ Regex rules
- ⬜ Spam rules
- ⬜ Automatic actions
- ⬜ Warning thresholds

---

## 21. Topics & Forums — ⬜

- ⬜ Topic detection
- ⬜ Topic routing
- ⬜ Topic-specific commands
- ⬜ Topic permissions
- ⬜ Topic-aware conversations

---

## 22. Telegram WebApp / Mini Apps — ⬜

- ⬜ Init data validation
- ⬜ Authentication
- ⬜ User integration
- ⬜ Secure backend communication
- ⬜ Laravel integration

---

## 23. Payments — ⬜

- ⬜ Invoices
- ⬜ Payment updates
- ⬜ Successful payments
- ⬜ Provider integration
- ⬜ Payment events

---

## 24. Broadcast System — ⬜

- ⬜ Batch sending
- ⬜ Segmentation
- ⬜ Scheduling
- ⬜ Queue integration
- ⬜ Delivery tracking
- ⬜ Failure handling
- ⬜ Rate limiting

---

## 25. Multi-Bot — ⬜

- ⬜ Multiple bot tokens
- ⬜ Bot-specific routes
- ⬜ Bot-specific middleware
- ⬜ Bot-specific configuration
- ⬜ Bot-specific conversations
- ⬜ Bot management

---

## 26. Plugin / Module Architecture — ⬜

- ⬜ Modules
- ⬜ Plugins
- ⬜ Lifecycle
- ⬜ Configuration
- ⬜ Plugin routes
- ⬜ Plugin middleware
- ⬜ Plugin events

---

# 🛠️ Developer Experience & Operations

## 27. Debug & Inspector — ⬜

- ⬜ Update inspector
- ⬜ Matched route
- ⬜ Middleware pipeline
- ⬜ Conversation state
- ⬜ API requests/responses
- ⬜ Debug mode
- ⬜ Diagnostics

---

## 28. Telegram Doctor — ⬜

```bash
php artisan telegram:doctor
```

- ⬜ Token check
- ⬜ API connection
- ⬜ Webhook
- ⬜ Polling
- ⬜ Routes
- ⬜ Cache
- ⬜ Queue
- ⬜ Permissions
- ⬜ Configuration

---

## 29. CI/CD & Compatibility — ⬜

- ⬜ GitHub Actions matrix
- ⬜ PHP versions
- ⬜ Laravel versions
- ⬜ Static analysis
- ⬜ Code style
- ⬜ Dependency checks
- ⬜ Automated releases

---

## 30. Documentation & Examples — ⬜

- ⬜ Documentation website
- ⬜ English docs
- ⬜ Persian docs
- ⬜ API reference
- ⬜ Installation/configuration
- ⬜ Webhook/polling
- ⬜ Routing/controllers
- ⬜ Middleware/conversations
- ⬜ Queue/testing
- ⬜ Keyboard/Response API
- ⬜ Example applications

---

## 31. AI-Friendly Documentation — ⬜

- ⬜ Structured API reference
- ⬜ Machine-readable examples
- ⬜ Task recipes
- ⬜ Troubleshooting knowledge base
- ⬜ LLM-friendly architecture guide

---

# 📍 CURRENT POSITION

```text
Current Foundation
       ↓
🔴 Completion Gate                    ← NOW
       ↓
1. Exception + Webhook Security
       ↓
2. Queue Reliability
       ↓
3. Testing / Fake Telegram
       ↓
4. Response API
       ↓
5. Keyboard Completion
       ↓
6. Outgoing Rate Limiter
       ↓
🟢 Foundation = 100%
       ↓
🚀 Regex Callback Query
       ↓
Bot Context
       ↓
Pagination
       ↓
Forms / Wizards
       ↓
User & Chat
       ↓
Deep Links
       ↓
Admin / Permissions
       ↓
Moderation / Topics
       ↓
WebApp / Payments
       ↓
Broadcast
       ↓
Multi-Bot
       ↓
Plugins
       ↓
Inspector / Doctor
       ↓
CI/CD
       ↓
Documentation
       ↓
AI Documentation
```

> **مرحله‌ی فعلی: Gate 1 — Exception + Webhook Security.**
> تا Gate 1 تا Gate 6 کامل نشوند، سراغ Regex Callback نمی‌رویم.