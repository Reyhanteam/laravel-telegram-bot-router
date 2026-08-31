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

    public static function onCommand(string $command, $callback): void
    {
        $command = '/'.ltrim($command, '/');
        static::addRoute('command', $command, $callback);
    }

    public static function onText(string $pattern, $callback): void
    {
        static::addRoute('text', $pattern, $callback);
    }

    public static function onCallbackQuery($callback): void
    {
        static::addRoute('callback_query', null, $callback);
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

        return static::addRouteWithMiddleware($type, $pattern, $callback, $middleware);
    }

    protected static function addRoute(string $type, ?string $pattern, $callback): TelegramRouteRegistrar
    {
        return static::addRouteWithMiddleware($type, $pattern, $callback, static::getGroupMiddleware());
    }

    protected static function addRouteWithMiddleware(string $type, ?string $pattern, $callback, array $middleware): TelegramRouteRegistrar
    {
        $route = [
            'type' => $type,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => static::getGroupMiddleware($middleware),
            'constraints' => [],
        ];

        static::$routes[] = $route;

        return new TelegramRouteRegistrar(count(static::$routes) - 1);
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
