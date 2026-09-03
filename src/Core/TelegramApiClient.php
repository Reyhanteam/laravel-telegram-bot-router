<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Core;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramApiException;
use RuntimeException;

/**
 * Shared Telegram Bot API HTTP client.
 *
 * The method registry is the single source of truth for parameter order and
 * required/optional metadata. This class only normalizes developer arguments
 * and sends the request. It does not duplicate HTTP logic per Telegram method.
 */
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

    /**
     * Supports both the legacy associative-array API and the ergonomic API:
     *
     *     $client->sendMessage($chatId, 'Hello');
     *     $client->sendMessage($chatId, 'Hello', parseMode: 'HTML');
     *     $client->sendMessage(['chat_id' => $chatId, 'text' => 'Hello']);
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (isset($arguments[0]) && is_array($arguments[0]) && $this->isAssociative($arguments[0])) {
            return $this->call($method, $arguments[0]);
        }

        return $this->call($method, $this->normalizeDeveloperArguments($method, $arguments));
    }

    /** @param list<mixed>|array<string, mixed> $arguments */
    private function normalizeDeveloperArguments(string $method, array $arguments): array
    {
        $definition = TelegramApiMethodRegistry::parameters($method);
        $names = TelegramApiMethodRegistry::parameterNames($method);

        $parameters = [];
        $position = 0;

        foreach ($arguments as $key => $value) {
            if (is_string($key)) {
                $apiName = $this->toApiParameterName($key);

                if (!in_array($apiName, $names, true)) {
                    throw new \InvalidArgumentException(sprintf('Unknown parameter [%s] for Telegram API method [%s].', $key, $method));
                }

                $parameters[$apiName] = $value;
                continue;
            }

            if (!array_key_exists($position, $names)) {
                throw new \ArgumentCountError(sprintf('Too many arguments for Telegram API method [%s].', $method));
            }

            $parameters[$names[$position]] = $value;
            $position++;
        }

        foreach ($definition['required'] as $required) {
            if (!array_key_exists($required, $parameters)) {
                throw new \ArgumentCountError(sprintf('Missing required parameter [%s] for Telegram API method [%s].', $required, $method));
            }
        }

        return array_filter($parameters, static fn ($value): bool => $value !== null);
    }

    private function toApiParameterName(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    /** @param array<mixed> $value */
    private function isAssociative(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
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
            $parts[] = [
                'name' => (string) $name,
                'contents' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value,
            ];
        }

        return $parts;
    }
}
