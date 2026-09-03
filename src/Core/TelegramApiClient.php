<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Core;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramApiException;
use RuntimeException;

final class TelegramApiClient
{
    public function __construct(
        private readonly ClientInterface $http,
        private readonly string $token,
        private readonly string $apiUrl = 'https://api.telegram.org',
    ) {
        if (trim($this->token) === '') {
            throw new RuntimeException('Telegram bot token is not configured.');
        }
    }

    public function call(string $method, array $parameters = []): mixed
    {
        if (!TelegramApiMethodRegistry::supports($method)) {
            throw new \BadMethodCallException(sprintf('Telegram API method [%s] is not supported.', $method));
        }

        $url = rtrim($this->apiUrl, '/') . '/bot' . $this->token . '/' . $method;
        $options = $this->buildRequestOptions($parameters);

        try {
            $response = $this->http->request('POST', $url, $options);
            $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $exception) {
            throw new TelegramApiException(
                sprintf('Telegram API request [%s] failed: %s', $method, $exception->getMessage()),
                (int) $exception->getCode(),
                [],
                $exception,
            );
        } catch (\JsonException $exception) {
            throw new TelegramApiException(
                sprintf('Telegram API returned invalid JSON for [%s].', $method),
                0,
                [],
                $exception,
            );
        }

        if (($payload['ok'] ?? false) !== true) {
            throw new TelegramApiException(
                (string) ($payload['description'] ?? sprintf('Telegram API method [%s] failed.', $method)),
                (int) ($payload['error_code'] ?? 0),
                is_array($payload['parameters'] ?? null) ? $payload['parameters'] : [],
            );
        }

        return $payload['result'] ?? null;
    }

    public function __call(string $method, array $arguments): mixed
    {
        $parameters = $arguments[0] ?? [];

        if (!is_array($parameters)) {
            throw new \InvalidArgumentException(sprintf('Parameters for [%s] must be an associative array.', $method));
        }

        return $this->call($method, $parameters);
    }

    /** @return array<string, mixed> */
    private function buildRequestOptions(array $parameters): array
    {
        if ($this->containsUpload($parameters)) {
            return ['multipart' => $this->toMultipart($parameters)];
        }

        return ['json' => $parameters];
    }

    private function containsUpload(array $parameters): bool
    {
        foreach ($parameters as $value) {
            if (is_resource($value) || $value instanceof \CURLFile) {
                return true;
            }
            if (is_array($value) && $this->containsUpload($value)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<array{name: string, contents: mixed}> */
    private function toMultipart(array $parameters): array
    {
        $parts = [];
        foreach ($parameters as $name => $value) {
            $parts[] = ['name' => (string) $name, 'contents' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value];
        }

        return $parts;
    }
}
