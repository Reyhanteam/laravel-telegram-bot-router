<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\TelegramBot;

class TelegramRouteConditionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $reflection = new \ReflectionClass(TelegramBot::class);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $property->setValue([]);
    }

    public function test_route_conditions_are_registered_as_middleware(): void
    {
        TelegramBot::onCommand('admin', fn () => null)
            ->adminOnly()
            ->whereUser(123456789)
            ->privateChat()
            ->userPermission('can_manage_chat');

        $route = TelegramBot::getRoutes()[0];
        $middleware = $route['middleware'];

        $this->assertContains('ReyhanTeam\\TelegramBotRouter\\Middleware\\TelegramAdminOnlyMiddleware', $middleware);
        $this->assertContains('ReyhanTeam\\TelegramBotRouter\\Middleware\\TelegramUserConditionMiddleware:123456789', $middleware);
        $this->assertContains('ReyhanTeam\\TelegramBotRouter\\Middleware\\TelegramChatTypeMiddleware:private', $middleware);
        $this->assertContains('ReyhanTeam\\TelegramBotRouter\\Middleware\\TelegramPermissionMiddleware:can_manage_chat', $middleware);
    }

    public function test_group_chat_accepts_group_and_supergroup(): void
    {
        TelegramBot::onCommand('group', fn () => null)->groupChat();

        $this->assertSame(
            ['ReyhanTeam\\TelegramBotRouter\\Middleware\\TelegramChatTypeMiddleware:group,supergroup'],
            TelegramBot::getRoutes()[0]['middleware']
        );
    }
}
