<?php

namespace ReyhanTeam\TelegramBotRouter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ReyhanTeam\TelegramBotRouter\TelegramUpdate;

class RouteMatched
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly TelegramUpdate $update,
        public readonly array $route,
    ) {
    }
}