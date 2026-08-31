<?php

namespace ReyhanTeam\TelegramBotRouter\Providers;

use ReyhanTeam\TelegramBotRouter\Exceptions\InvalidTelegramUpdateException;
use ReyhanTeam\TelegramBotRouter\Exceptions\TelegramApiException;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;
use Throwable;

class PollingProvider
{
    protected $router;
    protected int $interval;
    protected int $timeout;
    protected string $token;
    protected string $apiUrl;

    public function __construct($router, array $config)
    {
        $this->router = $router;
        $this->interval = max(0, (int) ($config['polling']['interval'] ?? 1500));
        $this->timeout = max(1, (int) ($config['polling']['timeout'] ?? 30));
        $this->token = (string) ($config['token'] ?? '');
        $this->apiUrl = rtrim((string) ($config['polling']['api_url'] ?? 'https://api.telegram.org'), '/');

        if ($this->token === '') {
            throw new \RuntimeException('Telegram bot token is not configured. Set TELEGRAM_BOT_TOKEN.');
        }
    }

    public function start(): void
    {
        $offset = 0;

        while (true) {
            try {
                $updates = $this->getUpdates($offset);

                foreach ($updates as $update) {
                    if (!is_array($update) || !isset($update['update_id'])) {
                        throw new InvalidTelegramUpdateException('Telegram polling response contained an invalid update.');
                    }

                    echo 'New update received: '.$update['update_id'].PHP_EOL;
                    $offset = (int) $update['update_id'] + 1;
                    $this->router->dispatch(TelegramUpdate::fromArray($update));
                }
            } catch (Throwable $e) {
                $this->router->handleException($e, ['source' => 'polling']);
            }

            if ($this->interval > 0) {
                usleep($this->interval * 1000);
            }
        }
    }

    private function getUpdates(int $offset): array
    {
        $url = $this->apiUrl.'/bot'.$this->token.'/getUpdates?offset='.$offset.'&timeout='.$this->timeout;
        $ch = curl_init($url);

        if ($ch === false) {
            throw new TelegramApiException('Unable to initialize cURL.', null, 'getUpdates');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => $this->timeout + 10,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelegramApiException('Telegram connection error: '.$error, null, 'getUpdates');
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);

        if (!is_array($data) || !isset($data['ok'])) {
            throw new TelegramApiException('Invalid response from Telegram API.', $httpCode, 'getUpdates');
        }

        if ($data['ok'] !== true) {
            throw new TelegramApiException(
                'Telegram API error: '.($data['description'] ?? 'Unknown error'),
                $httpCode,
                'getUpdates',
                isset($data['error_code']) ? (int) $data['error_code'] : null,
            );
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new TelegramApiException('Telegram API returned an unsuccessful HTTP status.', $httpCode, 'getUpdates');
        }

        return is_array($data['result'] ?? null) ? $data['result'] : [];
    }
}
