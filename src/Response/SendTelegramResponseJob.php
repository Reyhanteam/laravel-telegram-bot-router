<?php

declare(strict_types=1);

namespace ReyhanTeam\TelegramBotRouter\Response;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReyhanTeam\TelegramBotRouter\Core\TelegramApiClient;

final class SendTelegramResponseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @param array<string, mixed> $parameters */
    public function __construct(
        public readonly string $method,
        public readonly array $parameters,
    ) {
    }

    public function handle(TelegramApiClient $client): mixed
    {
        return $client->call($this->method, $this->parameters);
    }
}
