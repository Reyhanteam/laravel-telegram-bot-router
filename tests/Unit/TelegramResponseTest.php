<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use ReyhanTeam\TelegramBotRouter\Response\SendTelegramResponseJob;
use ReyhanTeam\TelegramBotRouter\Response\TelegramResponse;

final class TelegramResponseTest extends TestCase
{
    public function test_fluent_message_response_builds_and_sends(): void
    {
        $client = $this->client(['ok' => true, 'result' => ['message_id' => 101]]);

        $result = (new TelegramResponse($client, 123456789))
            ->message('<b>Hello</b>')
            ->parseMode('HTML')
            ->replyTo(100)
            ->replyMarkup(['inline_keyboard' => [[['text' => 'OK', 'callback_data' => 'ok']]]])
            ->send();

        $this->assertSame(['message_id' => 101], $result);
        $this->assertSame('/botTEST_TOKEN/sendMessage', $this->lastRequestPath());
        $this->assertSame([
            'chat_id' => 123456789,
            'text' => '<b>Hello</b>',
            'parse_mode' => 'HTML',
            'reply_parameters' => ['message_id' => 100],
            'reply_markup' => ['inline_keyboard' => [[['text' => 'OK', 'callback_data' => 'ok']]]],
        ], $this->lastRequestBody());
    }

    public function test_media_helpers_and_generic_options_are_fluent(): void
    {
        $client = $this->client(['ok' => true, 'result' => ['message_id' => 102]]);

        (new TelegramResponse($client))
            ->to(123456789)
            ->photo('https://example.com/photo.jpg', ['caption' => 'Test'])
            ->parseMode('HTML')
            ->send();

        $this->assertSame('/botTEST_TOKEN/sendPhoto', $this->lastRequestPath());
        $this->assertSame([
            'chat_id' => 123456789,
            'photo' => 'https://example.com/photo.jpg',
            'caption' => 'Test',
            'parse_mode' => 'HTML',
        ], $this->lastRequestBody());
    }

    public function test_edit_and_delete_helpers_build_the_expected_methods(): void
    {
        $client = $this->client(['ok' => true, 'result' => true]);
        (new TelegramResponse($client, 123456789))->editMessage(50, 'Updated')->send();
        $this->assertSame('/botTEST_TOKEN/editMessageText', $this->lastRequestPath());
        $this->assertSame(['chat_id' => 123456789, 'message_id' => 50, 'text' => 'Updated'], $this->lastRequestBody());

        $client = $this->client(['ok' => true, 'result' => true]);
        (new TelegramResponse($client, 123456789))->deleteMessage(50)->send();
        $this->assertSame('/botTEST_TOKEN/deleteMessage', $this->lastRequestPath());
        $this->assertSame(['chat_id' => 123456789, 'message_id' => 50], $this->lastRequestBody());
    }

    public function test_response_can_be_queued_as_a_serializable_job(): void
    {
        Queue::fake();
        $client = $this->client(['ok' => true, 'result' => true]);

        $response = (new TelegramResponse($client, 123456789))
            ->message('Queued')
            ->replyTo(42);

        $response->queue('telegram-high');

        Queue::assertPushed(SendTelegramResponseJob::class, function (SendTelegramResponseJob $job): bool {
            return $job->method === 'sendMessage'
                && $job->parameters === [
                    'chat_id' => 123456789,
                    'text' => 'Queued',
                    'reply_parameters' => ['message_id' => 42],
                ]
                && $job->queue === 'telegram-high';
        });
    }

    public function test_payload_is_serializable_without_the_http_client(): void
    {
        $client = $this->client(['ok' => true, 'result' => true]);
        $payload = (new TelegramResponse($client, 123456789))->message('Hello')->payload();

        $this->assertSame('sendMessage', $payload['method']);
        $this->assertSame(['chat_id' => 123456789, 'text' => 'Hello'], $payload['parameters']);
        $this->assertIsString(serialize($payload));
    }

    private function client(array $payload): TelegramApiClient
    {
        $handler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);
        $stack = HandlerStack::create($handler);
        $http = new Client(['handler' => $stack]);

        $this->handler = $handler;
        return new TelegramApiClient($http, 'TEST_TOKEN');
    }

    private function lastRequestPath(): string
    {
        $history = $this->handler->getLastRequest();
        return $history->getUri()->getPath();
    }

    /** @return array<string, mixed> */
    private function lastRequestBody(): array
    {
        $history = $this->handler->getLastRequest();
        return json_decode((string) $history->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    private MockHandler $handler;
}
