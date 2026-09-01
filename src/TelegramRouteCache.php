<?php

namespace ReyhanTeam\TelegramBotRouter;

use Illuminate\Support\Facades\Cache;

class TelegramRouteCache
{
    public const CACHE_KEY = 'telegram_bot_router.routes';

    public static function cache(): array
    {
        $routes = TelegramBot::getRoutes();
        $compiled = self::compile($routes);
        Cache::forever(self::key(), $compiled);
        return $compiled;
    }

    public static function clear(): bool
    {
        return Cache::forget(self::key());
    }

    public static function getCompiled(): ?array
    {
        $cached = Cache::get(self::key());
        return is_array($cached) ? $cached : null;
    }

    public static function getExactRouteIndex(array $route): ?int
    {
        $cached = self::getCompiled();
        if ($cached === null || ($cached['fingerprint'] ?? null) !== self::fingerprint(TelegramBot::getRoutes())) {
            return null;
        }

        $type = $route['type'] ?? null;
        $pattern = $route['pattern'] ?? null;
        if (!is_string($pattern) || !empty($route['constraints'])) {
            return null;
        }

        return match ($type) {
            'command' => $cached['exact_commands'][$pattern] ?? null,
            'text' => $cached['exact_text'][$pattern] ?? null,
            'callback_query' => $cached['exact_callbacks'][$pattern] ?? null,
            default => null,
        };
    }

    public static function fingerprint(array $routes): string
    {
        $signature = array_map(static function (array $route): array {
            $callback = $route['callback'] ?? null;
            if (is_array($callback)) {
                $callback = array_map(static function ($item) {
                    return is_object($item) ? get_class($item) : $item;
                }, $callback);
            } elseif (is_object($callback)) {
                $callback = get_class($callback);
            }

            return [
                'type' => $route['type'] ?? null,
                'pattern' => $route['pattern'] ?? null,
                'name' => $route['name'] ?? null,
                'middleware' => $route['middleware'] ?? [],
                'constraints' => $route['constraints'] ?? [],
                'parameters' => $route['parameters'] ?? [],
                'rate_limits' => $route['rate_limits'] ?? [],
                'queue' => $route['queue'] ?? [],
                'callback' => $callback,
            ];
        }, $routes);

        return hash('sha256', (string) json_encode($signature, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected static function compile(array $routes): array
    {
        $compiled = [
            'version' => 1,
            'fingerprint' => self::fingerprint($routes),
            'exact_commands' => [],
            'exact_text' => [],
            'exact_callbacks' => [],
        ];

        foreach ($routes as $index => $route) {
            if (!empty($route['constraints'])) {
                continue;
            }

            $pattern = $route['pattern'] ?? null;
            if (!is_string($pattern) || $pattern === '' || self::isRegex($pattern)) {
                continue;
            }

            $type = $route['type'] ?? null;
            if ($type === 'command' && !array_key_exists($pattern, $compiled['exact_commands'])) {
                $compiled['exact_commands'][$pattern] = $index;
            }
            if ($type === 'text' && !array_key_exists($pattern, $compiled['exact_text'])) {
                $compiled['exact_text'][$pattern] = $index;
            }
            if ($type === 'callback_query' && !array_key_exists($pattern, $compiled['exact_callbacks'])) {
                $compiled['exact_callbacks'][$pattern] = $index;
            }
        }

        return $compiled;
    }

    protected static function key(): string
    {
        return (string) config('telegram-bot-router.route_cache.key', self::CACHE_KEY);
    }

    protected static function isRegex(string $pattern): bool
    {
        if (strlen($pattern) < 3) return false;
        $delimiter = $pattern[0];
        if (ctype_alnum($delimiter) || $delimiter === '\\') return false;
        $length = strlen($pattern);
        $escaped = false;
        for ($i = 1; $i < $length; $i++) {
            $char = $pattern[$i];
            if ($escaped) { $escaped = false; continue; }
            if ($char === '\\') { $escaped = true; continue; }
            if ($char === $delimiter) {
                $modifiers = substr($pattern, $i + 1);
                return $modifiers === '' || preg_match('/^[a-zA-Z]*$/', $modifiers) === 1;
            }
        }
        return false;
    }
}
