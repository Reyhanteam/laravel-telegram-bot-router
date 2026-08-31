<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Contracts\Container\Container;
use ReyhanTeam\TelegramBotRouter\Middleware\TelegramMiddlewareRegistrar;

/**
 * TelegramBot router for defining Telegram update routes.
 */
class TelegramBot
{
    protected static $onInvalidAction = null;

    protected static ?Container $app = null;

    /**
     * @var array<int, array{type: string, pattern: ?string, callback: callable, middleware: array}>
     */
    protected static array $routes = [];

    protected static array $globalMiddleware = [];

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

    /**
     * Attach middleware to the route registered by the returned registrar.
     */
    public static function middleware(array $middleware): TelegramMiddlewareRegistrar
    {
        return new TelegramMiddlewareRegistrar($middleware);
    }

    /**
     * Register middleware for every Telegram route.
     */
    public static function globalMiddleware(array $middleware): void
    {
        static::$globalMiddleware = $middleware;
    }

    public static function getGlobalMiddleware(): array
    {
        return static::$globalMiddleware;
    }

    public static function fallback($callback): void
    {
        static::$fallback = $callback;
    }

    /**
     * @internal Used by TelegramMiddlewareRegistrar.
     */
    public static function addMiddlewareRoute(
        string $type,
        ?string $pattern,
        $callback,
        array $middleware
    ): void {
        if ($type === 'command' && $pattern !== null) {
            $pattern = '/'.ltrim($pattern, '/');
        }

        static::$routes[] = [
            'type' => $type,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $middleware,
        ];
    }

    protected static function addRoute(string $type, ?string $pattern, $callback): void
    {
        static::$routes[] = [
            'type' => $type,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => [],
        ];
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
