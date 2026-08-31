<?php

namespace ReyhanTeam\TelegramBotRouter\Middleware;

use Closure;
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
                    $instance = $this->resolve($middleware);

                    if (!method_exists($instance, 'handle')) {
                        throw new RuntimeException(sprintf(
                            'Telegram middleware [%s] must define a handle() method.',
                            is_object($instance) ? $instance::class : (string) $middleware
                        ));
                    }

                    return $instance->handle($update, $next);
                };
            },
            $destination
        );

        return $pipeline($update);
    }

    protected function resolve($middleware): object
    {
        if (is_object($middleware)) {
            return $middleware;
        }

        if (!is_string($middleware) || !class_exists($middleware)) {
            throw new RuntimeException(sprintf(
                'Telegram middleware [%s] could not be resolved.',
                (string) $middleware
            ));
        }

        return app()->make($middleware);
    }
}
