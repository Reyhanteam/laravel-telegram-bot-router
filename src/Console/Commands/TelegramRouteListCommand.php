<?php

namespace ReyhanTeam\TelegramBotRouter\Console\Commands;

use Illuminate\Console\Command;
use ReyhanTeam\TelegramBotRouter\TelegramBot;

class TelegramRouteListCommand extends Command
{
    protected $signature = 'reyhan:route-list';
    protected $description = 'List registered Telegram bot routes.';

    public function handle(): int
    {
        $routes = TelegramBot::getRoutes();
        if ($routes === []) {
            $this->components->warn('No Telegram routes are registered.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($routes as $index => $route) {
            $rows[] = [
                $index + 1,
                strtoupper((string) ($route['type'] ?? 'unknown')),
                $this->pattern($route),
                $this->name($route),
                $this->action($route['callback'] ?? null),
                $this->middleware($route),
                $this->rateLimits($route),
                $this->queue($route),
            ];
        }

        $this->newLine();
        $this->components->info('Telegram Bot Routes');
        $this->table(['#', 'TYPE', 'ROUTE', 'NAME', 'ACTION', 'MIDDLEWARE', 'RATE LIMIT', 'QUEUE'], $rows);
        $this->newLine();
        $this->components->twoColumnDetail('Total routes', (string) count($routes));
        return self::SUCCESS;
    }

    protected function pattern(array $route): string
    {
        $pattern = $route['pattern'] ?? null;
        return $pattern === null || $pattern === '' ? '*' : (string) $pattern;
    }

    protected function name(array $route): string
    {
        $name = $route['name'] ?? null;
        return $name === null || $name === '' ? '-' : (string) $name;
    }

    protected function action($callback): string
    {
        if ($callback instanceof \Closure) return 'Closure';
        if (is_array($callback) && count($callback) === 2) {
            $controller = is_object($callback[0] ?? null) ? get_class($callback[0]) : (string) ($callback[0] ?? '');
            return $controller.'@'.(string) ($callback[1] ?? '');
        }
        if (is_string($callback)) return $callback;
        return get_debug_type($callback);
    }

    protected function middleware(array $route): string
    {
        $middleware = $route['middleware'] ?? [];
        if ($middleware === []) return '-';
        return implode(', ', array_map(function ($item): string {
            if (is_string($item)) return $item;
            if (is_object($item)) return get_class($item);
            return get_debug_type($item);
        }, $middleware));
    }

    protected function rateLimits(array $route): string
    {
        $limits = $route['rate_limits'] ?? [];
        if ($limits === []) return '-';
        $items = [];
        foreach ($limits as $scope => $limit) {
            $max = $limit['max_attempts'] ?? $limit['max'] ?? '?';
            $decay = $limit['decay_seconds'] ?? $limit['decay'] ?? '?';
            $items[] = sprintf('%s:%s/%ss', $scope, $max, $decay);
        }
        return implode(', ', $items);
    }

    protected function queue(array $route): string
    {
        $queue = $route['queue'] ?? [];
        if (!($queue['enabled'] ?? false)) return '-';
        return $queue['queue'] ?? config('telegram-bot-router.queue.queue', 'default');
    }
}
