<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiMethodRegistry;

final class TelegramApiClientTest extends TestCase
{
    public function test_get_chat_sends_the_expected_request(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => ['id' => -1001234567890, 'type' => 'supergroup'],
        ]);

        $result = $client->getChat(['chat_id' => -1001234567890]);

        $this->assertSame(-1001234567890, $result['id']);
        $this->assertSame('/botTEST_TOKEN/getChat', $this->lastRequestPath());
        $this->assertSame(['chat_id' => -1001234567890], $this->lastRequestBody());
    }

    public function test_get_chat_member_count_sends_the_expected_request(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => 42,
        ]);

        $result = $client->getChatMemberCount(['chat_id' => -1001234567890]);

        $this->assertSame(42, $result);
        $this->assertSame('/botTEST_TOKEN/getChatMemberCount', $this->lastRequestPath());
        $this->assertSame(['chat_id' => -1001234567890], $this->lastRequestBody());
    }

    public function test_get_chat_member_sends_the_expected_request(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => [
                'user' => ['id' => 123456789, 'is_bot' => false, 'first_name' => 'Test'],
                'status' => 'member',
            ],
        ]);

        $result = $client->getChatMember([
            'chat_id' => -1001234567890,
            'user_id' => 123456789,
        ]);

        $this->assertSame('member', $result['status']);
        $this->assertSame('/botTEST_TOKEN/getChatMember', $this->lastRequestPath());
        $this->assertSame([
            'chat_id' => -1001234567890,
            'user_id' => 123456789,
        ], $this->lastRequestBody());
    }

    public function test_set_my_commands_sends_the_expected_request(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => true,
        ]);

        $commands = [
            ['command' => 'start', 'description' => 'Start the bot'],
            ['command' => 'help', 'description' => 'Show help'],
        ];

        $result = $client->setMyCommands([
            'commands' => $commands,
            'language_code' => 'en',
        ]);

        $this->assertTrue($result);
        $this->assertSame('/botTEST_TOKEN/setMyCommands', $this->lastRequestPath());
        $this->assertSame([
            'commands' => $commands,
            'language_code' => 'en',
        ], $this->lastRequestBody());
    }

    public function test_get_my_commands_sends_the_expected_request(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => [
                ['command' => 'start', 'description' => 'Start the bot'],
            ],
        ]);

        $result = $client->getMyCommands(['language_code' => 'en']);

        $this->assertCount(1, $result);
        $this->assertSame('start', $result[0]['command']);
        $this->assertSame('/botTEST_TOKEN/getMyCommands', $this->lastRequestPath());
        $this->assertSame(['language_code' => 'en'], $this->lastRequestBody());
    }

    public function test_send_message_accepts_positional_arguments_and_named_optional_arguments(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => ['message_id' => 100, 'chat' => ['id' => 123]],
        ]);

        $result = $client->sendMessage(123, 'Hello', parseMode: 'HTML');

        $this->assertSame(100, $result['message_id']);
        $this->assertSame('/botTEST_TOKEN/sendMessage', $this->lastRequestPath());
        $this->assertSame([
            'chat_id' => 123,
            'text' => 'Hello',
            'parse_mode' => 'HTML',
        ], $this->lastRequestBody());
    }

    public function test_pin_chat_message_accepts_positional_arguments(): void
    {
        $client = $this->client([
            'ok' => true,
            'result' => true,
        ]);

        $result = $client->pinChatMessage(-1001234567890, 321);

        $this->assertTrue($result);
        $this->assertSame('/botTEST_TOKEN/pinChatMessage', $this->lastRequestPath());
        $this->assertSame([
            'chat_id' => -1001234567890,
            'message_id' => 321,
        ], $this->lastRequestBody());
    }

    public function test_send_live_photo_requires_both_media_values_in_registry_order(): void
    {
        $this->assertSame(
            ['chat_id', 'live_photo', 'photo'],
            TelegramApiMethodRegistry::parameters('sendLivePhoto')['required'],
        );
    }

    public function test_registry_contains_exactly_the_package_api_methods(): void
    {
        $methods = TelegramApiMethodRegistry::methods();

        $this->assertCount(100, $methods);
        $this->assertCount(100, array_unique($methods));

        foreach ($methods as $method) {
            $definition = TelegramApiMethodRegistry::parameters($method);
            $this->assertSame(
                [...$definition['required'], ...$definition['optional']],
                TelegramApiMethodRegistry::parameterNames($method),
            );
        }
    }

    public function test_unknown_named_parameter_is_rejected(): void
    {
        $client = $this->client(['ok' => true, 'result' => true]);

        $this->expectException(\InvalidArgumentException::class);
        $client->pinChatMessage(123, 456, unknownOption: true);
    }

    public function test_missing_required_parameter_is_rejected(): void
    {
        $client = $this->client(['ok' => true, 'result' => true]);

        $this->expectException(\ArgumentCountError::class);
        $client->pinChatMessage(123);
    }

    /** @param array<string, mixed> $payload */
    private function client(array $payload): TelegramApiClient
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );

        $this->mock = new MockHandler([$response]);
        $stack = HandlerStack::create($this->mock);

        return new TelegramApiClient(
            new Client(['handler' => $stack]),
            'TEST_TOKEN',
        );
    }

    private function lastRequestPath(): string
    {
        return $this->mock->getLastRequest()->getUri()->getPath();
    }

    /** @return array<string, mixed> */
    private function lastRequestBody(): array
    {
        return json_decode((string) $this->mock->getLastRequest()->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    private MockHandler $mock;
}
