<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Testing;

use PHPUnit\Framework\Assert;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

/**
 * In-memory Telegram API fake for PHPUnit/Laravel tests.
 *
 * It never performs an HTTP request. Every API call is recorded and can be
 * inspected with assertions. Incoming Telegram updates can also be created
 * through the small factory helpers below.
 */
final class TelegramFake
{
    /** @var array<int, array{method: string, arguments: array<int|string, mixed>}> */
    private array $calls = [];

    /** @var array<string, mixed> */
    private array $responses = [];

    /** @var array<int, array<string, mixed>> */
    private array $updates = [];

    /** @var array<int, string> */
    private array $executedControllers = [];

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

    /** @param array<string, mixed> $message */
    public function receiveMessage(array $message): TelegramUpdate
    {
        return $this->receive([
            'update_id' => count($this->updates) + 1,
            'message' => $message,
        ]);
    }

    public function command(string $command, array $overrides = []): TelegramUpdate
    {
        $message = array_replace_recursive($this->defaultMessage('/'.ltrim($command, '/')), $overrides);

        return $this->receiveMessage($message);
    }

    public function message(string $text, array $overrides = []): TelegramUpdate
    {
        $message = array_replace_recursive($this->defaultMessage($text), $overrides);

        return $this->receiveMessage($message);
    }

    public function callbackQuery(string $data, array $overrides = []): TelegramUpdate
    {
        $callback = array_replace_recursive([
            'id' => 'callback-'.(count($this->updates) + 1),
            'from' => $this->defaultUser(),
            'message' => $this->defaultMessage('button'),
            'chat_instance' => 'test-chat-instance',
            'data' => $data,
        ], $overrides);

        return $this->receive([
            'update_id' => count($this->updates) + 1,
            'callback_query' => $callback,
        ]);
    }

    /** @param array<string, mixed> $update */
    public function update(array $update): TelegramUpdate
    {
        return $this->receive($update);
    }

    public function markControllerExecuted(string $controller, string $method): self
    {
        $this->executedControllers[] = $controller.'@'.$method;

        return $this;
    }

    public function assertApiCalled(string $method, ?callable $callback = null): void
    {
        $calls = $this->callsFor($method);
        Assert::assertNotEmpty($calls, sprintf('Telegram API method [%s] was not called.', $method));

        if ($callback !== null) {
            Assert::assertTrue($callback($calls), sprintf('Telegram API assertion for [%s] failed.', $method));
        }
    }

    /** @param array<int|string, mixed> $arguments */
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
                foreach ($call['arguments'] as $argument) {
                    if ($argument === $text) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    /** @param array<string, mixed> $keyboard */
    public function assertKeyboardSent(array $keyboard): void
    {
        $this->assertApiCalled('sendMessage', static function (array $calls) use ($keyboard): bool {
            foreach ($calls as $call) {
                foreach ($call['arguments'] as $argument) {
                    if (is_array($argument) && $argument === $keyboard) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    public function assertControllerExecuted(string $controller, string $method): void
    {
        Assert::assertContains(
            $controller.'@'.$method,
            $this->executedControllers,
            sprintf('Controller [%s@%s] was not executed.', $controller, $method)
        );
    }

    public function assertNoApiCall(string $method): void
    {
        Assert::assertCount(0, $this->callsFor($method), sprintf('Telegram API method [%s] was called.', $method));
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

    /** @return array<int, array{method: string, arguments: array<int|string, mixed>}> */
    private function callsFor(string $method): array
    {
        return array_values(array_filter(
            $this->calls,
            static fn (array $call): bool => $call['method'] === $method
        ));
    }

    /** @return array<string, mixed> */
    private function defaultUser(): array
    {
        return [
            'id' => 1001,
            'is_bot' => false,
            'first_name' => 'Test',
            'username' => 'test_user',
        ];
    }

    /** @return array<string, mixed> */
    private function defaultChat(): array
    {
        return [
            'id' => 2001,
            'type' => 'private',
            'first_name' => 'Test',
        ];
    }

    /** @return array<string, mixed> */
    private function defaultMessage(string $text): array
    {
        return [
            'message_id' => 4001,
            'from' => $this->defaultUser(),
            'chat' => $this->defaultChat(),
            'date' => 1700000000,
            'text' => $text,
        ];
    }
}
