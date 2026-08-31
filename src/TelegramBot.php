<?php

namespace ReyhanTeam\TelegramBotRouter;

use Closure;
use Illuminate\Contracts\Container\Container;
use ReyhanTeam\TelegramBotRouter\Conversation\ConversationRegistrar;
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareRegistrar;

/**
 * TelegramBot router for defining Telegram update routes.
 */
class TelegramBot
{
    protected static $onInvalidAction = null;
    protected static ?Container $app = null;
    protected static array $routes = [];
    protected static array $globalMiddleware = [];
    protected static array $middlewareGroupStack = [];
    protected static array $conversations = [];
    protected static $fallback = null;

    public static function onCommand(string $command, $callback): TelegramRouteRegistrar
    {
        $command = '/'.ltrim($command, '/');
        return static::addRoute('command', $command, $callback);
    }

    public static function onText(string $pattern, $callback): TelegramRouteRegistrar
    {
        return static::addRoute('text', $pattern, $callback);
    }

    public static function onCallbackQuery($callback): TelegramRouteRegistrar
    {
        return static::addRoute('callback_query', null, $callback);
    }

    public static function middleware(array $middleware): TelegramMiddlewareRegistrar
    {
        return new TelegramMiddlewareRegistrar($middleware);
    }

    public static function globalMiddleware(array $middleware): void
    {
        static::$globalMiddleware = $middleware;
    }

    public static function group(array $middleware, Closure $routes): void
    {
        static::$middlewareGroupStack[] = $middleware;

        try {
            $routes();
        } finally {
            array_pop(static::$middlewareGroupStack);
        }
    }

    public static function conversation(string $name): ConversationRegistrar
    {
        return new ConversationRegistrar($name);
    }

    public static function addConversation(string $name, array $steps, int $ttl = 3600): void
    {
        static::$conversations[$name] = [
            'steps' => array_values($steps),
            'ttl' => $ttl,
        ];
    }

    public static function getConversations(): array
    {
        return static::$conversations;
    }

    public static function getGlobalMiddleware(): array
    {
        return static::$globalMiddleware;
    }

    public static function fallback($callback): void
    {
        static::$fallback = $callback;
    }

    public static function addMiddlewareRoute(string $type, ?string $pattern, $callback, array $middleware): TelegramRouteRegistrar
    {
        if ($type === 'command' && $pattern !== null) {
            $pattern = '/'.ltrim($pattern, '/');
        }

        return static::addRouteWithMiddleware(
            $type,
            $pattern,
            $callback,
            static::getGroupMiddleware($middleware)
        );
    }

    protected static function addRoute(string $type, ?string $pattern, $callback): TelegramRouteRegistrar
    {
        return static::addRouteWithMiddleware(
            $type,
            $pattern,
            $callback,
            static::getGroupMiddleware()
        );
    }

    protected static function addRouteWithMiddleware(string $type, ?string $pattern, $callback, array $middleware): TelegramRouteRegistrar
    {
        static::$routes[] = [
            'type' => $type,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $middleware,
            'constraints' => [],
            'parameters' => static::extractRouteParameters($pattern),
        ];

        return new TelegramRouteRegistrar(count(static::$routes) - 1);
    }

    protected static function extractRouteParameters(?string $pattern): array
    {
        if ($pattern === null || static::isRegexPattern($pattern)) {
            return [];
        }

        preg_match_all('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', $pattern, $matches);

        return $matches[1] ?? [];
    }

    protected static function isRegexPattern(string $pattern): bool
    {
        if (strlen($pattern) < 3) {
            return false;
        }

        $delimiter = $pattern[0];
        if (ctype_alnum($delimiter) || $delimiter === '\\') {
            return false;
        }

        $length = strlen($pattern);
        $escaped = false;

        for ($i = 1; $i < $length; $i++) {
            $char = $pattern[$i];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === $delimiter) {
                $modifiers = substr($pattern, $i + 1);
                return $modifiers === '' || preg_match('/^[a-zA-Z]*$/', $modifiers) === 1;
            }
        }

        return false;
    }

    protected static function getGroupMiddleware(array $middleware = []): array
    {
        $groupMiddleware = [];

        foreach (static::$middlewareGroupStack as $group) {
            $groupMiddleware = array_merge($groupMiddleware, $group);
        }

        return array_merge($groupMiddleware, $middleware);
    }

    public static function addConstraint(int $routeIndex, string $name, string $expression): void
    {
        if (!isset(static::$routes[$routeIndex])) {
            throw new \OutOfBoundsException('Telegram route does not exist.');
        }

        static::$routes[$routeIndex]['constraints'][$name] = $expression;
    }

    public static function getRoutes(): array
    {
        return static::$routes;
    }

    public static function getFallback(): ?callable
    {
        return static::$fallback;
    }

    public static function setApplication(Container $app): void
    {
        static::$app = $app;
    }

    public static function getApplication(): ?Container
    {
        return static::$app;
    }

    public static function onInvalid($callback): void
    {
        self::$onInvalidAction = $callback;
    }

    public static function getOnInvalid()
    {
        return self::$onInvalidAction;
    }
}
