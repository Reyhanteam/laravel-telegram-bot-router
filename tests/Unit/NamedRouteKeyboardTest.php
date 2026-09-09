<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReyhanTeam\TelegramBotRouter\Keyboard\Keyboard;
use ReyhanTeam\TelegramBotRouter\TelegramBot;

final class KeyboardClassNameTestController
{
    public function test555($update): void
    {
    }
}

final class NamedRouteKeyboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $reflection = new \ReflectionClass(TelegramBot::class);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $property->setValue([]);
    }

    public function test_named_command_route_creates_internal_callback_alias(): void
    {
        $callback = static fn () => null;

        TelegramBot::onCommand('start', $callback)->name('start');

        $routes = TelegramBot::getRoutes();

        $this->assertCount(2, $routes);
        $this->assertSame('command', $routes[0]['type']);
        $this->assertSame('/start', $routes[0]['pattern']);
        $this->assertSame('start', $routes[0]['name']);

        $this->assertSame('callback_query', $routes[1]['type']);
        $this->assertSame('start', $routes[1]['pattern']);
        $this->assertSame($callback, $routes[1]['callback']);
        $this->assertTrue($routes[1]['internal']);
    }

    public function test_keyboard_route_name_generates_callback_data(): void
    {
        TelegramBot::onCommand('start', static fn () => null)->name('start');

        $keyboard = Keyboard::inline()
            ->callbackButton('👤 پروفایل', Keyboard::routeName('start'));

        $this->assertSame('start', $keyboard->toArray()['inline_keyboard'][0][0]['callback_data']);
    }

    public function test_keyboard_route_name_rejects_unknown_route(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telegram route [missing] was not found.');

        Keyboard::routeName('missing');
    }

    public function test_named_route_names_must_be_unique(): void
    {
        TelegramBot::onCommand('start', static fn () => null)->name('start');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telegram route name [start] is already in use.');

        TelegramBot::onCommand('profile', static fn () => null)->name('start');
    }

    public function test_keyboard_class_name_registers_controller_callback(): void
    {
        $callbackData = Keyboard::className(KeyboardClassNameTestController::class, 'test555');

        $this->assertSame(45, strlen($callbackData));
        $this->assertStringStartsWith('reyhan:class:', $callbackData);

        $keyboard = Keyboard::inline()
            ->callbackButton('تست ۵۵۵', $callbackData);

        $this->assertSame(
            $callbackData,
            $keyboard->toArray()['inline_keyboard'][0][0]['callback_data']
        );

        $routes = TelegramBot::getRoutes();
        $this->assertCount(1, $routes);
        $this->assertSame('callback_query', $routes[0]['type']);
        $this->assertSame($callbackData, $routes[0]['pattern']);
        $this->assertSame(
            [KeyboardClassNameTestController::class, 'test555'],
            $routes[0]['callback']
        );
        $this->assertTrue($routes[0]['internal']);
    }

    public function test_keyboard_class_name_rejects_unknown_controller(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telegram controller class [Tests\\Unit\\MissingController] was not found.');

        Keyboard::className('Tests\\Unit\\MissingController', 'test555');
    }

    public function test_keyboard_class_name_rejects_unknown_method(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Telegram controller method [missing] was not found on [Tests\\Unit\\KeyboardClassNameTestController].');

        Keyboard::className(KeyboardClassNameTestController::class, 'missing');
    }
}
