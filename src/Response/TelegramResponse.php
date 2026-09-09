<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Response;

use Illuminate\Contracts\Bus\Dispatcher;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;
use RuntimeException;

/**
 * High-level, fluent Telegram response builder.
 *
 * A response can be returned directly from a Telegram route. The router will
 * send it automatically. It can also be sent explicitly or queued.
 */
final class TelegramResponse
{
    private ?string $method = null;

    /** @var array<string, mixed> */
    private array $parameters = [];

    public function __construct(private readonly TelegramApiClient $client, int|string|null $chatId = null)
    {
        if ($chatId !== null) {
            $this->parameters['chat_id'] = $chatId;
        }
    }

    public function to(int|string $chatId): self
    {
        $this->parameters['chat_id'] = $chatId;
        return $this;
    }

    public function message(string $text): self
    {
        $this->method = 'sendMessage';
        $this->parameters['text'] = $text;
        return $this;
    }

    public function text(string $text): self
    {
        return $this->message($text);
    }

    public function photo(string $photo, array $options = []): self
    {
        return $this->media('sendPhoto', 'photo', $photo, $options);
    }

    public function audio(string $audio, array $options = []): self
    {
        return $this->media('sendAudio', 'audio', $audio, $options);
    }

    public function document(string $document, array $options = []): self
    {
        return $this->media('sendDocument', 'document', $document, $options);
    }

    public function video(string $video, array $options = []): self
    {
        return $this->media('sendVideo', 'video', $video, $options);
    }

    public function animation(string $animation, array $options = []): self
    {
        return $this->media('sendAnimation', 'animation', $animation, $options);
    }

    public function media(string $method, string $field, string $value, array $options = []): self
    {
        $this->method = $method;
        $this->parameters[$field] = $value;
        foreach ($options as $name => $option) {
            $this->parameters[$this->apiName((string) $name)] = $option;
        }
        return $this;
    }

    public function parseMode(string $parseMode): self
    {
        $this->parameters['parse_mode'] = $parseMode;
        return $this;
    }

    public function replyMarkup(array $replyMarkup): self
    {
        $this->parameters['reply_markup'] = $replyMarkup;
        return $this;
    }

    public function replyTo(int $messageId): self
    {
        $this->parameters['reply_parameters'] = ['message_id' => $messageId];
        return $this;
    }

    public function editMessage(int $messageId, string $text): self
    {
        $this->method = 'editMessageText';
        $this->parameters['message_id'] = $messageId;
        $this->parameters['text'] = $text;
        return $this;
    }

    public function deleteMessage(int $messageId): self
    {
        $this->method = 'deleteMessage';
        $this->parameters['message_id'] = $messageId;
        return $this;
    }

    public function method(string $method, array $parameters = []): self
    {
        $this->method = $method;
        foreach ($parameters as $name => $value) {
            $this->parameters[$this->apiName((string) $name)] = $value;
        }
        return $this;
    }

    public function option(string $name, mixed $value): self
    {
        $this->parameters[$this->apiName($name)] = $value;
        return $this;
    }

    public function send(): mixed
    {
        if ($this->method === null) {
            throw new RuntimeException('Telegram response has no response method.');
        }

        if ($this->requiresChatId() && !array_key_exists('chat_id', $this->parameters)) {
            throw new RuntimeException('Telegram response requires a chat_id.');
        }

        return $this->client->call($this->method, $this->parameters);
    }

    public function queue(?string $queue = null): mixed
    {
        if ($this->method === null) {
            throw new RuntimeException('Telegram response has no response method.');
        }

        $job = new SendTelegramResponseJob($this->method, $this->parameters);
        if ($queue !== null) {
            $job->onQueue($queue);
        }

        return app(Dispatcher::class)->dispatch($job);
    }

    /** @return array{method: string, parameters: array<string, mixed>} */
    public function payload(): array
    {
        if ($this->method === null) {
            throw new RuntimeException('Telegram response has no response method.');
        }

        return ['method' => $this->method, 'parameters' => $this->parameters];
    }

    private function requiresChatId(): bool
    {
        return !in_array($this->method, ['getMe', 'logOut', 'close'], true);
    }

    private function apiName(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
}
