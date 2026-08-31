<?php

namespace Tests\Unit;

use Closure;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Middleware\MiddlewarePipeline;

class MiddlewarePipelineTest extends TestCase
{
    public function test_middleware_runs_in_registered_order(): void
    {
        $order = [];
        $update = (object) ['id' => 1];

        $pipeline = new MiddlewarePipeline([
            function ($update, Closure $next) use (&$order) {
                $order[] = 'first-before';
                $result = $next($update);
                $order[] = 'first-after';
                return $result;
            },
            function ($update, Closure $next) use (&$order) {
                $order[] = 'second-before';
                $result = $next($update);
                $order[] = 'second-after';
                return $result;
            },
        ]);

        $result = $pipeline->send($update, function ($update) use (&$order) {
            $order[] = 'controller';
            return 'ok';
        });

        $this->assertSame('ok', $result);
        $this->assertSame([
            'first-before',
            'second-before',
            'controller',
            'second-after',
            'first-after',
        ], $order);
    }

    public function test_middleware_can_stop_pipeline(): void
    {
        $controllerCalled = false;
        $update = (object) ['id' => 1];

        $pipeline = new MiddlewarePipeline([
            function ($update, Closure $next) {
                return 'blocked';
            },
        ]);

        $result = $pipeline->send($update, function () use (&$controllerCalled) {
            $controllerCalled = true;
            return 'controller';
        });

        $this->assertSame('blocked', $result);
        $this->assertFalse($controllerCalled);
    }
}
