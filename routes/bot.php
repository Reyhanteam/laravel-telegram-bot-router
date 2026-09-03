<?php

declare(strict_types=1);

use ReyhanTeam\TelegramBotRouter\Facades\BOT;
use ReyhanTeam\TelegramBotRouter\Facades\Route;
/*
|--------------------------------------------------------------------------
| Telegram Bot Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for your Telegram bot. These routes
| are kept separate from Laravel web routes and are handled by the package.
|
*/

Route::onCommand('start', function ($update) {
    return BOT::sendMessage([
        'chat_id' => $update->message->chat->id,
        'text' => 'Welcome! Type /help to see available commands.',
    ]);
});
