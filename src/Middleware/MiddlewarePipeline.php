<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
use ReyhanTeam\TelegramBotRouter\TelegramBot;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use RuntimeException;

class MiddlewarePipeline
{
    public function __construct(protected array $middleware = [])
    {
    }

    public function process(TelegramUpdate $update, Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            function (Closure $next, $middleware): Closure {
                return function (TelegramUpdate $update) use ($middleware, $next): mixed {
                    [$instance, $parameters] = $this->resolve($middleware);

                    if (!method_exists($instance, 'handle')) {
                        throw new RuntimeException(sprintf(
                            'Telegram middleware [%s] must define a handle() method.',
                            is_object($instance) ? $instance::class : (string) $middleware
                        ));
                    }

                    return $instance->handle($update, $next, ...$parameters);
                };
            },
            $destination
        );

        return $pipeline($update);
    }

    protected function resolve($middleware): array
    {
        if (is_object($middleware)) {
            return [$middleware, []];
        }

        if (!is_string($middleware) || $middleware === '') {
            throw new RuntimeException(sprintf(
                'Telegram middleware [%s] could not be resolved.',
                (string) $middleware
            ));
        }

        [$name, $parameters] = $this->parseMiddleware($middleware);
        $class = TelegramBot::resolveMiddlewareAlias($name) ?? $name;

        if (!class_exists($class)) {
            throw new RuntimeException(sprintf(
                'Telegram middleware [%s] could not be resolved.',
                $name
            ));
        }

        return [app()->make($class), $parameters];
    }

    protected function parseMiddleware(string $middleware): array
    {
        $parts = explode(':', $middleware, 2);

        if (count($parts) === 1) {
            return [$parts[0], []];
        }

        $parameters = $parts[1] === ''
            ? []
            : str_getcsv($parts[1]);

        return [$parts[0], $parameters];
    }
}
