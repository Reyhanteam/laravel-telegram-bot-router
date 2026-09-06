<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Testing;

use PHPUnit\Framework\Assert;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

final class TelegramFake
{
    /** @var array<int, array{method: string, arguments: array<int|string, mixed>}> */
    private array $calls = [];

    /** @var array<string, mixed> */
    private array $responses = [];

    /** @var array<int, array<string, mixed>> */
    private array $updates = [];

    public function __call(string $method, array $arguments): mixed
    {
        $this->calls[] = ['method' => $method, 'arguments' => $arguments];

        return $this->responses[$method] ?? true;
    }

    public function respond(string $method, mixed $response): self
    {
        $this->responses[$method] = $response;

        return $this;
    }

    /** @param array<string, mixed> $update */
    public function receive(array $update): TelegramUpdate
    {
        $this->updates[] = $update;

        return TelegramUpdate::fromArray($update);
    }

    public function assertApiCalled(string $method, ?callable $callback = null): void
    {
        $calls = array_values(array_filter($this->calls, static fn (array $call): bool => $call['method'] === $method));
        Assert::assertNotEmpty($calls, sprintf('Telegram API method [%s] was not called.', $method));

        if ($callback !== null) {
            Assert::assertTrue($callback($calls), sprintf('Telegram API assertion for [%s] failed.', $method));
        }
    }

    /** @param array<int, mixed> $arguments */
    public function assertApiCalledWith(string $method, array $arguments): void
    {
        $this->assertApiCalled($method, static function (array $calls) use ($arguments): bool {
            foreach ($calls as $call) {
                if ($call['arguments'] === $arguments) {
                    return true;
                }
            }

            return false;
        });
    }

    public function assertCommandReceived(string $command): void
    {
        $expected = '/'.ltrim($command, '/');
        foreach ($this->updates as $update) {
            $text = $update['message']['text'] ?? null;
            if (is_string($text) && preg_split('/\s+/', trim($text))[0] === $expected) {
                return;
            }
        }

        Assert::fail(sprintf('Telegram command [%s] was not received.', $expected));
    }

    public function assertMessageReceived(string $text): void
    {
        foreach ($this->updates as $update) {
            if (($update['message']['text'] ?? null) === $text) {
                return;
            }
        }

        Assert::fail(sprintf('Telegram message [%s] was not received.', $text));
    }

    public function assertCallbackReceived(string $data): void
    {
        foreach ($this->updates as $update) {
            if (($update['callback_query']['data'] ?? null) === $data) {
                return;
            }
        }

        Assert::fail(sprintf('Telegram callback [%s] was not received.', $data));
    }

    public function assertMessageSent(string $text): void
    {
        $this->assertApiCalled('sendMessage', static function (array $calls) use ($text): bool {
            foreach ($calls as $call) {
                if (($call['arguments'][1] ?? null) === $text || ($call['arguments']['text'] ?? null) === $text) {
                    return true;
                }
            }

            return false;
        });
    }

    public function assertKeyboardSent(array $keyboard): void
    {
        $this->assertApiCalled('sendMessage', static function (array $calls) use ($keyboard): bool {
            foreach ($calls as $call) {
                $arguments = $call['arguments'];
                if (($arguments['replyMarkup'] ?? $arguments['reply_markup'] ?? null) === $keyboard) {
                    return true;
                }
            }

            return false;
        });
    }

    public function assertControllerExecuted(string $controller, string $method): void
    {
        $key = $controller.'@'.$method;
        $this->assertApiCalled('controller:'.$key);
    }

    /** @return array<int, array{method: string, arguments: array<int|string, mixed>}> */
    public function calls(): array
    {
        return $this->calls;
    }

    /** @return array<int, array<string, mixed>> */
    public function updates(): array
    {
        return $this->updates;
    }
}
