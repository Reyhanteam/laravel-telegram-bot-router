# 🗺️ ReyhanTeam Laravel Telegram Bot Router — Development Roadmap

> «A Laravel-native routing and management system for Telegram bots.»

این فایل نقشه‌ی اصلی توسعه‌ی `Reyhanteam/laravel-telegram-bot-router` است.

هدف این Roadmap این است که بدانیم **الان پروژه دقیقاً کجاست، چه چیزهایی واقعاً پیاده‌سازی شده، و قدم بعدی چیست**.
ترتیب بخش‌های پایین بر اساس **اهمیت فنی و وابستگی قابلیت‌ها به یکدیگر** تنظیم شده است؛ بنابراین از بالا به پایین جلو می‌رویم و هر مرحله را قبل از رفتن به مرحله‌ی بعد کامل و تست می‌کنیم.

---

## 📊 وضعیت‌ها

| وضعیت | معنی |
|---|---|
| ✅ Completed | در Repository پیاده‌سازی شده و قابل استفاده است |
| 🟡 In Progress | هسته یا بخشی از قابلیت وجود دارد، اما هنوز باید کامل و production-ready شود |
| ⬜ Planned | هنوز پیاده‌سازی نشده است |

> این وضعیت‌ها بر اساس بررسی مستقیم Repository فعلی تنظیم شده‌اند، نه Roadmap قدیمی.

---

# 🟢 بخش A — آنچه تاکنون ساخته شده است

## 1. Core Telegram Routing — ✅

هسته‌ی اصلی پروژه کامل است.

- ✅ Laravel Composer Package
- ✅ GitHub Repository
- ✅ Packagist
- ✅ Service Provider
- ✅ `routes/bot.php`
- ✅ Telegram Webhook
- ✅ Telegram Polling
- ✅ `BOT::onCommand()`
- ✅ `BOT::onText()`
- ✅ `BOT::onCallbackQuery()`
- ✅ Closure handlers
- ✅ Controller + method handlers
- ✅ Laravel Service Container resolution
- ✅ Dependency Injection
- ✅ TelegramUpdate wrapper
- ✅ Route matching engine
- ✅ Exact matching
- ✅ Text regex matching
- ✅ Route parameters
- ✅ Route constraints
- ✅ Command arguments
- ✅ Fallback routes
- ✅ Invalid update handling
- ✅ Telegram update type routing

### Update Types موجود

- ✅ Message
- ✅ Callback Query
- ✅ Inline Query
- ✅ Edited Message
- ✅ Channel Post
- ✅ Edited Channel Post
- ✅ Chat Member
- ✅ My Chat Member
- ✅ Chat Join Request

---

## 2. Middleware System — ✅

سیستم Middleware در حال حاضر یک pipeline واقعی Laravel-style دارد.

- ✅ Global Telegram middleware
- ✅ Route middleware
- ✅ Middleware groups
- ✅ Nested groups
- ✅ Middleware parameters
- ✅ Named middleware aliases
- ✅ Configuration aliases
- ✅ Container resolution
- ✅ Middleware objects
- ✅ Short-circuit
- ✅ Execution order
- ✅ Optional `TelegramMiddlewareInterface`

---

## 3. Conversation & State — ✅

Conversation system نسبت به Roadmap قبلی جلوتر رفته و بخش‌های اصلی آن پیاده‌سازی شده‌اند.

- ✅ Per-user / per-chat conversation state
- ✅ Conversation steps
- ✅ Wait for next message
- ✅ Save current step
- ✅ Move to next step
- ✅ Finish conversation
- ✅ Timeout
- ✅ Conversation data
- ✅ Laravel Cache storage
- ✅ Closure steps
- ✅ Controller steps
- ✅ Cancel Conversation API / command
- ✅ Input validation helpers
- ✅ Explicit conversation middleware
- ✅ Conversation events
- ✅ Storage driver selection / `cacheStore()`

---

## 4. Exception, Error & Safety Foundation — 🟡

هسته‌ی Error Handling ساخته شده، اما hardening امنیتی و production checks هنوز باید کامل‌تر شوند.

- ✅ Telegram route exceptions
- ✅ Invalid update exceptions
- ✅ Telegram API exceptions
- ✅ Configurable exception handler
- ✅ Safe logging foundation
- ✅ Sensitive-data protection foundation
- 🟡 Webhook verification / authentication
- 🟡 Security policy and hardening documentation

---

## 5. Events — ✅

- ✅ Update Received
- ✅ Message Received
- ✅ Command Received
- ✅ Callback Query Received
- ✅ Route Matched
- ✅ Conversation Started
- ✅ Conversation Step Completed
- ✅ Conversation Finished
- ✅ Conversation Cancelled
- ✅ Conversation Timed Out

---

## 6. Route Management & Conditions — ✅

- ✅ `php artisan reyhan:route-list`
- ✅ Named Telegram routes
- ✅ Route cache
- ✅ Route clear
- ✅ Admin-only routes
- ✅ User conditions
- ✅ Private chat conditions
- ✅ Group chat conditions
- ✅ Channel conditions
- ✅ User permission checks
- ✅ Chat type constraints

---

## 7. Telegram API Foundation — 🟡

Low-level Telegram API support already exists and the API method registry/client are in the Repository.

- ✅ Telegram API client
- ✅ Telegram API method registry
- ✅ Developer-friendly API method surface / facade foundation
- 🟡 Unified high-level response API
- 🟡 Media abstraction
- 🟡 Better response objects / fluent responses

---

## 8. Keyboard Foundation — 🟡

Keyboard functionality already exists and has been used with real Telegram Callback Query routing, اما هنوز Builder نهایی و کامل نیست.

- 🟡 Inline keyboard builder
- 🟡 Inline buttons
- 🟡 Callback buttons
- 🟡 URL buttons
- ⬜ Reply keyboard builder
- ⬜ WebApp buttons
- ⬜ Login buttons
- ⬜ Switch inline query buttons
- ⬜ Callback-data helpers
- ⬜ Dynamic buttons
- ⬜ Conditional buttons
- ⬜ Keyboard factories
- ⬜ Reusable keyboards

---

## 9. Queue Foundation — 🟡

Queue core در پروژه وجود دارد، اما reliability کامل آن هنوز تمام نشده است.

- ✅ Laravel Queue integration
- ✅ Queue update processing
- ✅ Queue message/task support
- ✅ Queued Telegram route support
- 🟡 Retry strategy
- 🟡 Failed-job handling
- 🟡 Failure events / observability
- 🟡 Backoff configuration
- 🟡 Production queue documentation

---

## 10. Testing & Fake Telegram Foundation — 🟡

Testing infrastructure اکنون وجود دارد و در حال تکمیل است.

- ✅ `Telegram::fake()` foundation
- ✅ Fake Telegram API calls
- ✅ Fake API responses
- ✅ Fake messages
- ✅ Fake commands
- ✅ Fake callback queries
- ✅ Fake incoming updates
- ✅ API assertions
- ✅ Message assertions
- ✅ Keyboard assertions
- ✅ Callback assertions
- ✅ Controller execution assertions / integration coverage
- 🟡 Full route-testing helpers
- 🟡 Middleware integration coverage
- 🟡 Conversation integration coverage
- 🟡 Queue integration coverage
- 🟡 Rate-limit integration coverage
- 🟡 Polling test strategy
- 🟡 Complete PHPUnit integration suite

> تست‌های Callback Query و Webhook integration اخیراً به Repository اضافه شده‌اند و اجرای واقعی Callback Query + Polling نیز قبلاً با یک Bot واقعی تأیید شده است.

---

# 🔴 بخش B — مسیر توسعه از اینجا به بعد

> **از این قسمت به بعد Backlog است.**
> ما قابلیت‌ها را از بالا به پایین اجرا می‌کنیم.

---

# 1️⃣ Next — Regex Callback Query Routing — ⬜

**این مرحله‌ی بعدی پروژه است.**

هدف:

```php
BOT::onCallbackQuery('/^user:(\\d+)$/', [UserController::class, 'show']);
```

و سپس:

```text
user:123
user:456
```

به همان Route برسند و captureها در `TelegramUpdate` در دسترس باشند.

### کارهای این مرحله

- ⬜ تشخیص صحیح Regex در Callback Query
- ⬜ Regex capture groups
- ⬜ ذخیره‌ی `$update->matches`
- ⬜ پشتیبانی از named capture groups
- ⬜ ترکیب Regex با constraints در صورت نیاز
- ⬜ حفظ exact matching فعلی
- ⬜ حفظ callback route parameters فعلی
- ⬜ تعریف scoring صحیح بین exact / parameter / regex / generic routes
- ⬜ تست unit کامل
- ⬜ تست integration برای webhook
- ⬜ تست integration برای polling path
- ⬜ مستندسازی API

**خروجی مورد انتظار:** Callback Query routing از نظر قدرت matching هم‌سطح Text routing شود.

---

# 2️⃣ Queue Reliability — Retry & Failed Jobs — ⬜

بعد از کامل شدن Router matching، Queue باید production-ready شود.

### کارها

- ⬜ Retry policy
- ⬜ Configurable attempts
- ⬜ Backoff
- ⬜ Retryable exceptions
- ⬜ Non-retryable exceptions
- ⬜ Failed job handling
- ⬜ Failed Telegram update tracking
- ⬜ Queue failure events
- ⬜ Logging بدون افشای token
- ⬜ Queue-specific configuration
- ⬜ Tests برای retry/failure

**خروجی:** اگر Telegram update یا task شکست خورد، سیستم رفتار قابل پیش‌بینی و قابل بازیابی داشته باشد.

---

# 3️⃣ Testing System — Complete Fake Telegram — ⬜

Testing باید از یک foundation به یک test suite حرفه‌ای تبدیل شود.

### API نهایی هدف

```php
Telegram::fake();

Telegram::assertApiCalled('sendMessage');
Telegram::assertMessageSent('Hello');
Telegram::assertCallbackReceived('profile');
Telegram::assertControllerExecuted(
    UserController::class,
    'profile'
);
```

### کارها

- ⬜ Route test helpers
- ⬜ Command assertions
- ⬜ Text assertions
- ⬜ Callback assertions
- ⬜ Middleware integration tests
- ⬜ Conversation integration tests
- ⬜ Queue integration tests
- ⬜ Rate-limit integration tests
- ⬜ Webhook integration suite
- ⬜ Polling testable architecture
- ⬜ API response mocking کامل
- ⬜ Example test suite
- ⬜ Laravel Testbench compatibility verification

**خروجی:** توسعه‌دهنده بتواند تقریباً تمام رفتار Bot را بدون Telegram واقعی تست کند.

---

# 4️⃣ Bot Context — ⬜

یک Context واحد برای هر Update ایجاد می‌کنیم تا Controllerها و Middlewareها مجبور نباشند اطلاعات را از چند جای مختلف جمع کنند.

### Context باید بتواند شامل این موارد باشد

- ⬜ Current update
- ⬜ User
- ⬜ Chat
- ⬜ Current route
- ⬜ Route parameters
- ⬜ Command arguments
- ⬜ Conversation state
- ⬜ Middleware/shared state
- ⬜ Telegram API client
- ⬜ Bot information

هدف API احتمالی:

```php
public function handle(TelegramContext $context)
{
    $context->user();
    $context->chat();
    $context->update();
    $context->route();
}
```

**نکته:** Context باید روی معماری فعلی ساخته شود و نباید `TelegramUpdate` فعلی را بی‌دلیل بشکند.

---

# 5️⃣ Telegram Response API — ⬜

بعد از Context، لایه‌ی Response را استاندارد می‌کنیم.

هدف:

```php
return TelegramResponse::text('Hello');
```

یا:

```php
return TelegramResponse::photo($photo);
```

### کارها

- ⬜ Text response
- ⬜ Photo response
- ⬜ Video response
- ⬜ Audio response
- ⬜ Document response
- ⬜ Voice response
- ⬜ Animation response
- ⬜ Reply markup integration
- ⬜ Parse mode
- ⬜ Reply-to-message
- ⬜ Edit/delete response helpers
- ⬜ Response objects
- ⬜ Controller/Closure return handling
- ⬜ Tests

**هدف:** برنامه‌نویس به‌جای درگیر شدن با جزئیات low-level API، Response قابل پیش‌بینی داشته باشد.

---

# 6️⃣ Keyboard Builder — ⬜ Complete

Keyboard فعلی را به یک Builder کامل و پایدار تبدیل می‌کنیم.

### کارها

- ⬜ Inline keyboard
- ⬜ Reply keyboard
- ⬜ Callback buttons
- ⬜ URL buttons
- ⬜ WebApp buttons
- ⬜ Login buttons
- ⬜ Switch inline query buttons
- ⬜ Row management
- ⬜ Multiple rows
- ⬜ Button chaining
- ⬜ Dynamic buttons
- ⬜ Conditional buttons
- ⬜ Keyboard factories
- ⬜ Reusable keyboards
- ⬜ Callback data helpers
- ⬜ Validation of Telegram button structures
- ⬜ Tests
- ⬜ Documentation

---

# 7️⃣ Outgoing Telegram Rate Limiter — ⬜

Rate limiting ورودی وجود دارد؛ حالا باید ارسال به Telegram API نیز کنترل شود.

- ⬜ Per-bot outgoing limits
- ⬜ Queue-aware throttling
- ⬜ Retry-after handling
- ⬜ Backoff
- ⬜ Configurable limits
- ⬜ Tests

---

# 8️⃣ Pagination — ⬜

بعد از Response + Keyboard، Pagination معنی واقعی پیدا می‌کند.

- ⬜ Previous / Next
- ⬜ Page numbers
- ⬜ Callback-based pagination
- ⬜ Collection integration
- ⬜ Custom pagination views
- ⬜ Query/data preservation
- ⬜ Tests

---

# 9️⃣ Forms & Wizards — ⬜

Conversation foundation حالا آماده است تا به Form/Wizard تبدیل شود.

- ⬜ Multi-step forms
- ⬜ Validation
- ⬜ Previous step
- ⬜ Next step
- ⬜ Cancel
- ⬜ Confirmation
- ⬜ Conversation integration
- ⬜ Form state
- ⬜ Tests

---

# 🔟 User & Chat Abstraction — ⬜

بعد از Context، abstractionهای سطح بالاتر ساخته می‌شوند.

- ⬜ Telegram User object
- ⬜ Telegram Chat object
- ⬜ User metadata
- ⬜ Chat metadata
- ⬜ User state
- ⬜ Chat state
- ⬜ Laravel model integration (اختیاری و جدا از core)

---

# 1️⃣1️⃣ Deep Links — ⬜

- ⬜ `/start` parameters
- ⬜ Referral tracking helpers
- ⬜ Campaign parameters
- ⬜ Onboarding helpers
- ⬜ Tests

---

# 1️⃣2️⃣ Security Hardening — 🟡 → ⬜

Foundation وجود دارد، اما قبل از production ecosystem باید کامل شود.

- 🟡 Safe token handling
- 🟡 Safe logging
- ⬜ Webhook secret/token verification
- ⬜ Replay/abuse considerations
- ⬜ Input hardening
- ⬜ Security documentation
- ⬜ Security policy

---

# 1️⃣3️⃣ Multi-Bot — ⬜

پس از پایدار شدن Context و APIها:

- ⬜ Multiple bot tokens
- ⬜ Bot-specific configuration
- ⬜ Bot-specific routes
- ⬜ Bot-specific middleware
- ⬜ Bot-specific conversations
- ⬜ Bot context isolation
- ⬜ Bot management

---

# 1️⃣4️⃣ Admin & Permission System — 🟡 → ⬜

شرط‌ها و `adminOnly()` foundation فعلی هستند؛ سیستم کامل Role/Permission هنوز باقی است.

- 🟡 Admin detection foundation
- 🟡 Permission checks foundation
- ⬜ Roles
- ⬜ Permissions registry
- ⬜ Command permissions
- ⬜ Chat permissions
- ⬜ Moderation permissions
- ⬜ Laravel authorization integration

---

# 1️⃣5️⃣ Group Moderation — ⬜

- ⬜ Ban / unban
- ⬜ Restrict users
- ⬜ Warning system
- ⬜ Spam detection
- ⬜ Link filtering
- ⬜ Word filtering
- ⬜ User management
- ⬜ Moderation logs

---

# 1️⃣6️⃣ Auto Moderation — ⬜

- ⬜ Rule engine
- ⬜ Custom rules
- ⬜ Regex rules
- ⬜ Spam rules
- ⬜ Automatic actions
- ⬜ Warning thresholds
- ⬜ Moderation events

---

# 1️⃣7️⃣ Topics & Forums — ⬜

- ⬜ Topic detection
- ⬜ Topic routing
- ⬜ Topic-specific commands
- ⬜ Topic permissions
- ⬜ Topic-aware conversations

---

# 1️⃣8️⃣ Telegram WebApp / Mini Apps — ⬜

- ⬜ Init data validation
- ⬜ WebApp authentication
- ⬜ User integration
- ⬜ Secure backend communication
- ⬜ Laravel integration

---

# 1️⃣9️⃣ Payments — ⬜

- ⬜ Invoice handling
- ⬜ Payment updates
- ⬜ Successful payments
- ⬜ Provider integration
- ⬜ Order integration
- ⬜ Payment events

---

# 2️⃣0️⃣ Broadcast System — ⬜

- ⬜ Broadcast messages
- ⬜ User segmentation
- ⬜ Batch sending
- ⬜ Scheduling
- ⬜ Queue integration
- ⬜ Delivery tracking
- ⬜ Failure handling
- ⬜ Rate limiting

---

# 2️⃣1️⃣ Plugin / Module Architecture — ⬜

- ⬜ Modules
- ⬜ Plugins
- ⬜ Plugin lifecycle
- ⬜ Plugin configuration
- ⬜ Plugin routes
- ⬜ Plugin middleware
- ⬜ Plugin events

---

# 2️⃣2️⃣ Debug & Inspector — ⬜

- ⬜ Update inspector
- ⬜ Matched route viewer
- ⬜ Middleware pipeline viewer
- ⬜ Conversation state viewer
- ⬜ API request viewer
- ⬜ API response viewer
- ⬜ Debug mode
- ⬜ Error diagnostics

---

# 2️⃣3️⃣ Telegram Doctor — ⬜

```bash
php artisan telegram:doctor
```

Checks:

- ⬜ Bot token/configuration
- ⬜ Telegram API connectivity
- ⬜ Webhook configuration
- ⬜ Polling configuration
- ⬜ Route configuration
- ⬜ Cache
- ⬜ Queue
- ⬜ Permissions
- ⬜ Laravel configuration

---

# 2️⃣4️⃣ CI/CD & Compatibility — 🟡

GitHub Actions foundation already exists؛ اما matrix و quality gates باید کامل شوند.

- 🟡 GitHub Actions
- 🟡 Automated test workflow
- ⬜ PHP version matrix
- ⬜ Laravel version matrix
- ⬜ Static analysis
- ⬜ Code style checks
- ⬜ Dependency checks
- ⬜ Automated release workflow
- ⬜ Coverage reporting

---

# 2️⃣5️⃣ Documentation & Examples — 🟡

Documentation داخل Repository وجود دارد و چند guide تخصصی نیز اضافه شده‌اند، اما documentation product-level هنوز کامل نیست.

- 🟡 README
- 🟡 Callback Query guide
- 🟡 Keyboard guide
- 🟡 Route conditions guide
- 🟡 Update types guide
- ⬜ Complete API reference
- ⬜ Installation guide
- ⬜ Configuration guide
- ⬜ Webhook guide
- ⬜ Polling guide
- ⬜ Routing guide
- ⬜ Controllers guide
- ⬜ Middleware guide
- ⬜ Conversations guide
- ⬜ Queue guide
- ⬜ Testing guide
- ⬜ Example applications
- ⬜ Documentation website
- ⬜ Persian documentation
- ⬜ English documentation

---

# 2️⃣6️⃣ AI-Friendly Documentation — ⬜

در آخر documentation را برای استفاده توسط AI coding assistants نیز استاندارد می‌کنیم.

- ⬜ Structured API reference
- ⬜ Machine-readable examples
- ⬜ Common task recipes
- ⬜ Troubleshooting guide
- ⬜ Architecture guide
- ⬜ Migration guide
- ⬜ Agent-friendly conventions

---

# 🏁 مسیر اجرایی نهایی

برای اینکه پروژه پراکنده نشود، از اینجا به بعد ترتیب اصلی توسعه این است:

```text
CURRENT
  │
  ├── Core Routing                    ✅
  ├── Middleware                      ✅
  ├── Conversation                    ✅
  ├── Events                          ✅
  ├── Route Conditions/Management     ✅
  ├── API Foundation                  🟡
  ├── Keyboard Foundation             🟡
  ├── Queue Foundation                🟡
  └── Testing Foundation              🟡
  │
  ▼
1. Regex Callback Routing             ⬜  ← NEXT
  │
  ▼
2. Queue Retry + Failed Jobs          ⬜
  │
  ▼
3. Complete Testing / Fake Telegram   ⬜
  │
  ▼
4. Bot Context                        ⬜
  │
  ▼
5. Telegram Response API              ⬜
  │
  ▼
6. Complete Keyboard Builder          ⬜
  │
  ▼
7. Outgoing Rate Limiter               ⬜
  │
  ▼
8. Pagination                          ⬜
  │
  ▼
9. Forms & Wizards                     ⬜
  │
  ▼
10. User & Chat Abstraction            ⬜
  │
  ▼
11. Deep Links                         ⬜
  │
  ▼
12. Security Hardening                 🟡
  │
  ▼
13. Multi-Bot                          ⬜
  │
  ▼
14. Admin & Permissions                🟡
  │
  ▼
15. Group Moderation                   ⬜
  │
  ▼
16. Auto Moderation                    ⬜
  │
  ▼
17. Topics & Forums                    ⬜
  │
  ▼
18. WebApp / Mini Apps                 ⬜
  │
  ▼
19. Payments                           ⬜
  │
  ▼
20. Broadcast                          ⬜
  │
  ▼
21. Plugin Architecture                ⬜
  │
  ▼
22. Debug / Inspector                  ⬜
  │
  ▼
23. Telegram Doctor                    ⬜
  │
  ▼
24. CI/CD & Compatibility              🟡
  │
  ▼
25. Documentation & Examples           🟡
  │
  ▼
26. AI-Friendly Documentation          ⬜
```

---

# 🎯 قانون توسعه‌ی پروژه

از اینجا به بعد برای هر مرحله این چرخه را اجرا می‌کنیم:

```text
1. بررسی کد فعلی
      ↓
2. طراحی API و معماری
      ↓
3. پیاده‌سازی بدون شکستن API قبلی
      ↓
4. Unit Tests
      ↓
5. Integration Tests
      ↓
6. بررسی Laravel compatibility
      ↓
7. مستندسازی
      ↓
8. Commit
      ↓
9. Consumer App Smoke Test
      ↓
10. رفتن به مرحله بعد
```

> **مرحله‌ی بعدی فعلی: `Regex Callback Query Routing`.**
> فعلاً سراغ Pagination، Forms، Multi-Bot یا قابلیت‌های بزرگ‌تر نمی‌رویم تا این مرحله کامل، تست‌شده و پایدار شود.