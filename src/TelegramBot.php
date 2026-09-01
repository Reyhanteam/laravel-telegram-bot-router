<?php

namespace ReyhanTeam\TelegramBotRouter;

use Closure;
use Illuminate\Contracts\Container\Container;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationRegistrar;
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareRegistrar;

class TelegramBot
{
    protected static $onInvalidAction = null;
    protected static ?Container $app = null;
    protected static array $routes = [];
    protected static array $globalMiddleware = [];
    protected static array $middlewareAliases = [];
    protected static array $middlewareGroupStack = [];
    protected static array $conversations = [];
    protected static array $conversationCancelCommands = [];
    protected static $fallback = null;

    public static function onCommand(string $command, $callback): TelegramRouteRegistrar
    {
        $command = '/'.ltrim($command, '/');
        return static::addRoute('command', $command, $callback);
    }

    public static function onText(string $pattern, $callback): TelegramRouteRegistrar { return static::addRoute('text', $pattern, $callback); }
    public static function onCallbackQuery($callback): TelegramRouteRegistrar { return static::addRoute('callback_query', null, $callback); }
    public static function onInlineQuery($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('inline_query', $pattern, $callback); }
    public static function onEditedMessage($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('edited_message', $pattern, $callback); }
    public static function onChannelPost($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('channel_post', $pattern, $callback); }
    public static function onEditedChannelPost($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('edited_channel_post', $pattern, $callback); }
    public static function onChatMember($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('chat_member', $pattern, $callback); }
    public static function onMyChatMember($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('my_chat_member', $pattern, $callback); }
    public static function onChatJoinRequest($pattern, $callback = null): TelegramRouteRegistrar { return static::addFlexibleUpdateRoute('chat_join_request', $pattern, $callback); }

    protected static function addFlexibleUpdateRoute(string $type, $pattern, $callback = null): TelegramRouteRegistrar
    {
        if ($callback === null) { $callback = $pattern; $pattern = null; }
        if (!is_callable($callback) && !(is_array($callback) && count($callback) === 2)) {
            throw new \InvalidArgumentException("Invalid Telegram {$type} route action provided.");
        }
        return static::addRoute($type, is_string($pattern) ? $pattern : null, $callback);
    }

    public static function middleware(array $middleware): TelegramMiddlewareRegistrar { return new TelegramMiddlewareRegistrar($middleware); }
    public static function globalMiddleware(array $middleware): void { static::$globalMiddleware = $middleware; }
    public static function aliasMiddleware(string $name, string $middleware): void { static::$middlewareAliases[$name] = $middleware; }
    public static function aliasMiddlewares(array $aliases): void { foreach ($aliases as $name => $middleware) static::aliasMiddleware((string) $name, $middleware); }
    public static function getMiddlewareAliases(): array { return static::$middlewareAliases; }
    public static function resolveMiddlewareAlias(string $name): ?string { return static::$middlewareAliases[$name] ?? null; }
    public static function group(array $middleware, Closure $routes): void { static::$middlewareGroupStack[] = $middleware; try { $routes(); } finally { array_pop(static::$middlewareGroupStack); } }
    public static function conversation(string $name): ConversationRegistrar { return new ConversationRegistrar($name); }
    public static function addConversation(string $name, array $steps, int $ttl = 3600, array $middleware = [], ?string $cacheStore = null): void { static::$conversations[$name] = ['steps' => array_values($steps), 'ttl' => $ttl, 'middleware' => $middleware, 'cache_store' => $cacheStore]; }
    public static function getConversations(): array { return static::$conversations; }
    public static function cancelConversation(TelegramUpdate $update): bool { return app(\ReyhanTeam\TelegramBotRouter\Conversation\ConversationManager::class)->cancel($update); }
    public static function cancelConversationOnCommand(string $command): void { $command = '/'.ltrim($command, '/'); if (!in_array($command, static::$conversationCancelCommands, true)) static::$conversationCancelCommands[] = $command; }
    public static function getConversationCancelCommands(): array { return static::$conversationCancelCommands; }
    public static function getGlobalMiddleware(): array { return static::$globalMiddleware; }
    public static function fallback($callback): void { static::$fallback = $callback; }

    public static function addMiddlewareRoute(string $type, ?string $pattern, $callback, array $middleware): TelegramRouteRegistrar
    {
        if ($type === 'command' && $pattern !== null) $pattern = '/'.ltrim($pattern, '/');
        return static::addRouteWithMiddleware($type, $pattern, $callback, static::getGroupMiddleware($middleware));
    }
    public static function addRateLimit(int $routeIndex, string $scope, int $maxAttempts, int $decaySeconds): void { if (!isset(static::$routes[$routeIndex])) throw new \OutOfBoundsException('Telegram route does not exist.'); if (!in_array($scope, ['user', 'chat', 'command'], true)) throw new \InvalidArgumentException('Telegram rate limit scope must be user, chat, or command.'); static::$routes[$routeIndex]['rate_limits'][$scope] = ['enabled' => true, 'max_attempts' => max(1, $maxAttempts), 'decay_seconds' => max(1, $decaySeconds)]; }
    public static function addRateLimits(int $routeIndex, array $limits): void { foreach ($limits as $scope => $limit) { if (is_int($limit)) { static::addRateLimit($routeIndex, (string) $scope, $limit, 60); continue; } if (!is_array($limit)) throw new \InvalidArgumentException('Telegram rate limit configuration must be an integer or array.'); static::addRateLimit($routeIndex, (string) $scope, (int) ($limit['max_attempts'] ?? $limit['max'] ?? 60), (int) ($limit['decay_seconds'] ?? $limit['decay'] ?? 60)); } }
    public static function enableQueue(int $routeIndex, ?string $queue = null): void { if (!isset(static::$routes[$routeIndex])) throw new \OutOfBoundsException('Telegram route does not exist.'); static::$routes[$routeIndex]['queue'] = ['enabled' => true, 'queue' => $queue]; }
    public static function nameRoute(int $routeIndex, string $name): void { if (!isset(static::$routes[$routeIndex])) throw new \OutOfBoundsException('Telegram route does not exist.'); $name = trim($name); if ($name === '') throw new \InvalidArgumentException('Telegram route name cannot be empty.'); foreach (static::$routes as $index => $route) if ($index !== $routeIndex && ($route['name'] ?? null) === $name) throw new \InvalidArgumentException(sprintf('Telegram route name [%s] is already in use.', $name)); static::$routes[$routeIndex]['name'] = $name; }
    public static function getRouteByName(string $name): ?array { foreach (static::$routes as $route) if (($route['name'] ?? null) === $name) return $route; return null; }
    protected static function addRoute(string $type, ?string $pattern, $callback): TelegramRouteRegistrar { return static::addRouteWithMiddleware($type, $pattern, $callback, static::getGroupMiddleware()); }
    protected static function addRouteWithMiddleware(string $type, ?string $pattern, $callback, array $middleware): TelegramRouteRegistrar { static::$routes[] = ['type' => $type, 'pattern' => $pattern, 'callback' => $callback, 'name' => null, 'middleware' => $middleware, 'constraints' => [], 'parameters' => static::extractRouteParameters($pattern), 'rate_limits' => [], 'queue' => ['enabled' => false, 'queue' => null]]; return new TelegramRouteRegistrar(count(static::$routes) - 1); }
    protected static function extractRouteParameters(?string $pattern): array { if ($pattern === null || static::isRegexPattern($pattern)) return []; preg_match_all('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', $pattern, $matches); return $matches[1] ?? []; }
    protected static function isRegexPattern(string $pattern): bool { if (strlen($pattern) < 3) return false; $delimiter = $pattern[0]; if (ctype_alnum($delimiter) || $delimiter === '\\') return false; $length = strlen($pattern); $escaped = false; for ($i = 1; $i < $length; $i++) { $char = $pattern[$i]; if ($escaped) { $escaped = false; continue; } if ($char === '\\') { $escaped = true; continue; } if ($char === $delimiter) { $modifiers = substr($pattern, $i + 1); return $modifiers === '' || preg_match('/^[a-zA-Z]*$/', $modifiers) === 1; } } return false; }
    protected static function getGroupMiddleware(array $middleware = []): array { $groupMiddleware = []; foreach (static::$middlewareGroupStack as $group) $groupMiddleware = array_merge($groupMiddleware, $group); return array_merge($groupMiddleware, $middleware); }
    public static function addConstraint(int $routeIndex, string $name, string $expression): void { if (!isset(static::$routes[$routeIndex])) throw new \OutOfBoundsException('Telegram route does not exist.'); static::$routes[$routeIndex]['constraints'][$name] = $expression; }
    public static function getRoutes(): array { return static::$routes; }
    public static function getFallback(): ?callable { return static::$fallback; }
    public static function setApplication(Container $app): void { static::$app = $app; }
    public static function getApplication(): ?Container { return static::$app; }
    public static function onInvalid($callback): void { self::$onInvalidAction = $callback; }
    public static function getOnInvalid() { return self::$onInvalidAction; }
}
